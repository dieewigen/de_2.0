<?php
/** Layout-Fuß: schließt <main> und das Dokument, zeigt die Renderzeit. */
$__layout_dt = isset($GLOBALS['__layout_t0']) ? microtime(true) - $GLOBALS['__layout_t0'] : 0.0;
?>
<div class="render-time">Seite erstellt in <?= sprintf('%.3f', $__layout_dt) ?> s</div>
</main>
</div>
</body>
</html>
