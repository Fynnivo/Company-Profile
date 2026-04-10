<!-- ═══════════════════════════════════════════════════════
     Jodit WYSIWYG Editor — Full Featured Configuration
     100% free · MIT license · No API key · No banner
     CDN: cdn.jsdelivr.net (stable, fast globally)
     ═══════════════════════════════════════════════════════ -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jodit@4/es2021/jodit.min.css">
<script src="https://cdn.jsdelivr.net/npm/jodit@4/es2021/jodit.min.js"></script>
<style>
  /* ── Make Jodit fit nicely in the admin card ── */
  .jodit-container { border-radius: 12px !important; border-color: #e2e8f0 !important; }
  .jodit-toolbar__box { border-radius: 12px 12px 0 0 !important; }
  .jodit-workplace { border-radius: 0 0 12px 12px !important; }
  .jodit-status-bar { border-radius: 0 0 12px 12px !important; font-family: 'Nunito', sans-serif !important; font-size: 11px !important; }
  .jodit-toolbar-button__text { font-family: 'Nunito', sans-serif !important; }
</style>
<script>
(function() {

  // ── Upload config (reused for both image drag-drop and toolbar button) ──
  const uploaderConfig = {
    url: '<?= BASE_URL ?>/admin/articles/upload-image.php',
    format: 'json',
    pathVariableName: 'file',
    prepareData: function(data) { return data; },
    isSuccess: function(resp) { 
      console.log('Upload response:', resp);
      return resp.location || resp.files ? true : false; 
    },
    getMsg: function(resp) { 
      return resp.msg || resp.error || 'Upload failed'; 
    },
    process: function(resp) {
      console.log('Processing upload response:', resp);
      return {
        files:   resp.files || [resp.location],
        path:    '',
        baseurl: '',
        error:   resp.error || 0,
        msg:     resp.msg   || '',
      };
    },
    defaultHandlerSuccess: function(data) {
      console.log('Handler success - data:', data);
      var i, field = 'files';
      if (data[field] && data[field].length) {
        for (i = 0; i < data[field].length; i++) {
          console.log('Inserting image:', data[field][i]);
          this.s.insertImage(data[field][i], null, 300);
        }
      }
    },
  };

  // ── Full editor config ──────────────────────────────────────
  Jodit.make('#jodit-editor', {

    // ── Dimensions ───────────────────────────────────────────
    height:    550,
    minHeight: 300,
    maxHeight: 900,

    // ── Language & direction ─────────────────────────────────
    language:  'en',
    direction: 'ltr',

    // ── Toolbar ──────────────────────────────────────────────
    toolbarSticky:       true,   // toolbar stays visible when scrolling
    toolbarStickyOffset: 0,
    toolbarAdaptive:     true,   // responsive toolbar on small screens

    // ── Full toolbar — desktop (all features) ─────────────────
    buttons: [
      // Paragraph style
      'paragraph',
      '|',
      // Text formatting
      'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript',
      '|',
      // Font controls
      'font', 'fontsize',
      '|',
      // Color
      'brush',          // text color + background color
      '|',
      // Lists & indent
      'ul', 'ol', 'outdent', 'indent',
      '|',
      // Alignment
      'align',
      '|',
      // Media & links
      'image', 'video', 'file', 'link', 'unlink',
      '|',
      // Table
      'table',
      '|',
      // Insert
      'hr', 'symbol', 'classSpan',
      '|',
      // Utilities
      'copyformat',     // copy formatting (like Format Painter)
      'eraser',         // clear formatting
      '|',
      // Find & Replace
      'find',
      '|',
      // History
      'undo', 'redo',
      '|',
      // View
      'preview',        // preview in popup
      'print',          // print content
      'fullsize',       // fullscreen mode
      '|',
      // Source code
      'source',
    ],

    // ── Medium screens (tablet) ───────────────────────────────
    buttonsMD: [
      'paragraph', '|',
      'bold', 'italic', 'underline', '|',
      'brush', '|',
      'ul', 'ol', '|',
      'align', '|',
      'image', 'link', 'table', '|',
      'find', '|',
      'undo', 'redo', '|',
      'fullsize', 'source',
    ],

    // ── Small screens ─────────────────────────────────────────
    buttonsSM: [
      'bold', 'italic', '|',
      'ul', 'ol', '|',
      'image', 'link', '|',
      'undo', 'redo', '|',
      'fullsize',
    ],

    // ── Extra small ───────────────────────────────────────────
    buttonsXS: [
      'bold', 'italic', '|',
      'ul', 'ol', '|',
      'undo', 'redo',
    ],

    // ── Paragraph styles dropdown ─────────────────────────────
    defaultActionOnPaste: 'insert_clear_html',
    processPasteHTML:     true,

    // ── Editor content style ─────────────────────────────────
    style: {
      font:           "15px/1.8 'Nunito', sans-serif",
      color:          '#374151',
      padding:        '12px 16px',
      minHeight:      '200px',
    },

    // ── Font options ──────────────────────────────────────────
    fontValues: {
      'Nunito':        'Nunito, sans-serif',
      'Arial':         'Arial, sans-serif',
      'Georgia':       'Georgia, serif',
      'Times New Roman':'Times New Roman, serif',
      'Courier New':   'Courier New, monospace',
      'Verdana':       'Verdana, sans-serif',
      'Tahoma':        'Tahoma, sans-serif',
      'Trebuchet MS':  'Trebuchet MS, sans-serif',
    },

    // ── Font size options ─────────────────────────────────────
    fontSizeValues: ['10','11','12','13','14','15','16','18','20','22','24','26','28','32','36','48','64'],

    // ── Color picker presets ──────────────────────────────────
    colorPickerDefaultTab: 'color',
    colors: {
      greyscale:  ['#000000','#434343','#666666','#999999','#b7b7b7','#cccccc','#d9d9d9','#efefef','#f3f3f3','#ffffff'],
      palette:    ['#ff0000','#ff4500','#ff7700','#ffaa00','#ffcc00','#ffff00','#00ff00','#00ffaa','#00aaff','#0000ff','#7700ff','#ff00ff'],
      brand:      ['#111827','#1f2937','#374151','#4b5563','#6b7280','#2563eb','#1d4ed8','#3b82f6','#60a5fa','#93c5fd'],
    },

    // ── Image settings ────────────────────────────────────────
    uploader: uploaderConfig,
    image: {
      openOnDblClick:   true,
      editSrc:          true,
      useImageEditor:   false,
    },

    // ── Table settings ────────────────────────────────────────
    table: {
      allowCellSelection:  true,
      allowCellResize:     true,
      selectionCellStyle: 'background-color: #dbeafe;',
    },

    // ── Link settings ─────────────────────────────────────────
    link: {
      formTemplate:        null,
      followOnDblClick:    true,
      processVideoLink:    true,
      processPastedLink:   true,
      noFollowCheckbox:    true,
      openInNewTabCheckbox:true,
      modeClassName:       'link',
    },

    // ── Status bar ────────────────────────────────────────────
    showCharsCounter:   true,
    showWordsCounter:   true,
    showXPathInStatusbar: false,

    // ── Spellcheck ────────────────────────────────────────────
    spellcheck: true,

    // ── Clean paste ───────────────────────────────────────────
    cleanHTML: {
      cleanOnPaste: true,
      replaceNBSP:  true,
      allowTags: {
        a:          { href: true, target: true, rel: true },
        img:        { src: true, alt: true, style: true, width: true, height: true, class: true },
        p:          true,
        br:         true,
        strong:     true,
        b:          true,
        em:         true,
        i:          true,
        u:          true,
        s:          true,
        del:        true,
        h1:         true,
        h2:         true,
        h3:         true,
        h4:         true,
        h5:         true,
        h6:         true,
        ul:         true,
        ol:         true,
        li:         true,
        blockquote: true,
        pre:        true,
        code:       true,
        hr:         true,
        table:      true,
        thead:      true,
        tbody:      true,
        tr:         true,
        th:         { colspan: true, rowspan: true, style: true },
        td:         { colspan: true, rowspan: true, style: true },
        div:        { class: true, style: true },
        span:       { class: true, style: true },
        figure:     true,
        figcaption: true,
        sup:        true,
        sub:        true,
      },
    },

    // ── Drag & drop files ─────────────────────────────────────
    enableDragAndDropFileToEditor: true,

    // ── Placeholder ───────────────────────────────────────────
    placeholder: 'Mulai menulis konten artikel di sini...',

    // ── Ask before delete ─────────────────────────────────────
    askBeforePasteHTML:     false,
    askBeforePasteFromWord: false,

    // ── Readonly when needed ──────────────────────────────────
    readonly: false,

    // ── iframe mode: false = inline (better for admin panels) ─
    iframe: false,

    // ── Autofocus ─────────────────────────────────────────────
    autofocus: false,

    // ── Save on Ctrl+S ────────────────────────────────────────
    saveHeightInStorage: true,

  });

})();
</script>