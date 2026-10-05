(function () {
  // Buenos Aires date and time
  var reloj = document.getElementById('reloj');
  if (reloj) {
    var hora = function () {
      try {
        var ahora = new Date();
        var zona = 'America/Argentina/Buenos_Aires';
        var dia = new Intl.DateTimeFormat('es-AR', { weekday: 'long', day: 'numeric', month: 'long', timeZone: zona }).format(ahora);
        var hm = new Intl.DateTimeFormat('es-AR', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: zona }).format(ahora);
        reloj.textContent = dia.charAt(0).toUpperCase() + dia.slice(1) + ', ' + hm + ' h';
      } catch (e) {
        reloj.textContent = '';
      }
    };
    hora();
    setInterval(hora, 30000);
  }

  // Collapsible search
  var abrir = document.getElementById('abrir-buscador');
  var buscador = document.getElementById('buscador');
  if (abrir && buscador) {
    abrir.addEventListener('click', function () {
      var abierto = buscador.hidden;
      buscador.hidden = !abierto;
      abrir.setAttribute('aria-expanded', String(abierto));
      if (abierto) {
        var campo = buscador.querySelector('input[type="search"]');
        if (campo) { campo.focus(); }
      }
    });
  }

  // Front page: section filter and load-more without a page reload.
  // If anything fails, the links still work and lead to the section or the next page.
  var grilla = document.getElementById('grilla');
  var filtros = document.getElementById('filtros');
  if (!grilla || !filtros || !window.AN || !window.fetch) { return; }

  var botonMas = document.getElementById('cargar-mas');
  var cajaMas = botonMas ? botonMas.parentNode : null;
  var tanda = parseInt(grilla.getAttribute('data-tanda'), 10) || 9;
  var excluir = grilla.getAttribute('data-excluir') || '';
  var estado = { seccion: '', pagina: 1, ocupado: false };

  function texto(html) {
    return new DOMParser().parseFromString(String(html || ''), 'text/html').body.textContent || '';
  }
  function crear(etiqueta, attrs, hijos) {
    var e = document.createElement(etiqueta);
    Object.keys(attrs || {}).forEach(function (k) {
      if (k === 'texto') { e.textContent = attrs[k]; } else { e.setAttribute(k, attrs[k]); }
    });
    (hijos || []).forEach(function (h) { if (h) { e.appendChild(h); } });
    return e;
  }
  function fotoDe(nota) {
    var media = nota._embedded && nota._embedded['wp:featuredmedia'] && nota._embedded['wp:featuredmedia'][0];
    if (media) {
      var t = media.media_details && media.media_details.sizes;
      if (t && t.medium_large && t.medium_large.source_url) { return t.medium_large.source_url; }
      if (media.source_url) { return media.source_url; }
    }
    return nota.jetpack_featured_media_url || '';
  }
  function seccionDe(nota) {
    var grupos = (nota._embedded && nota._embedded['wp:term']) || [];
    for (var i = 0; i < grupos.length; i++) {
      for (var j = 0; j < (grupos[i] || []).length; j++) {
        var term = grupos[i][j];
        if (term.taxonomy === 'category' && term.slug !== 'uncategorized' && term.slug !== 'sin-categoria') { return term; }
      }
    }
    return null;
  }
  function fechaDe(nota) {
    try {
      return new Intl.DateTimeFormat('es-AR', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(nota.date));
    } catch (e) {
      return '';
    }
  }
  function tarjeta(nota) {
    var url = fotoDe(nota);
    var img = url ? crear('img', { src: url, alt: '', loading: 'lazy' }) : null;
    if (img) { img.addEventListener('error', function () { img.remove(); }); }
    var seccion = seccionDe(nota);
    return crear('article', { class: 'tarjeta' }, [
      crear('a', { class: 'foto', href: nota.link, tabindex: '-1', 'aria-hidden': 'true' }, [img]),
      crear('div', {}, [
        seccion ? crear('a', { class: 'etiqueta', href: seccion.link, texto: seccion.name }) : null,
        crear('h3', {}, [crear('a', { href: nota.link, texto: texto(nota.title && nota.title.rendered) })]),
        crear('time', { class: 'fecha', datetime: nota.date, texto: fechaDe(nota) })
      ])
    ]);
  }

  function pedir(reemplazar, alFallar) {
    if (estado.ocupado) { return; }
    estado.ocupado = true;
    grilla.classList.add('cargando');

    var params = ['per_page=' + tanda, 'page=' + estado.pagina, '_embed=1'];
    if (excluir) { params.push('exclude=' + encodeURIComponent(excluir)); }
    if (estado.seccion) { params.push('categories=' + encodeURIComponent(estado.seccion)); }
    var url = AN.rest + (AN.rest.indexOf('?') === -1 ? '?' : '&') + params.join('&');

    fetch(url, { credentials: 'same-origin' })
      .then(function (r) {
        if (!r.ok) { throw new Error('rest'); }
        var paginas = parseInt(r.headers.get('X-WP-TotalPages'), 10) || 1;
        return r.json().then(function (notas) { return { notas: notas, paginas: paginas }; });
      })
      .then(function (res) {
        if (reemplazar) { grilla.textContent = ''; }
        res.notas.forEach(function (n) { grilla.appendChild(tarjeta(n)); });
        if (reemplazar && !res.notas.length) {
          grilla.appendChild(crear('p', { class: 'vacio', texto: 'Todavía no hay notas en esta sección.' }));
        }
        if (cajaMas) { cajaMas.hidden = estado.pagina >= res.paginas; }
        estado.ocupado = false;
        grilla.classList.remove('cargando');
      })
      .catch(function () {
        estado.ocupado = false;
        grilla.classList.remove('cargando');
        alFallar();
      });
  }

  filtros.addEventListener('click', function (e) {
    var enlace = e.target.closest ? e.target.closest('a[data-seccion]') : null;
    if (!enlace) { return; }
    e.preventDefault();
    var destino = enlace.href;
    estado.seccion = enlace.getAttribute('data-seccion');
    estado.pagina = 1;
    Array.prototype.forEach.call(filtros.querySelectorAll('a'), function (a) {
      a.setAttribute('aria-current', String(a === enlace));
    });
    pedir(true, function () { window.location.href = destino; });
  });

  if (botonMas) {
    botonMas.addEventListener('click', function (e) {
      e.preventDefault();
      var destino = botonMas.href;
      estado.pagina += 1;
      pedir(false, function () { window.location.href = destino; });
    });
  }
})();
