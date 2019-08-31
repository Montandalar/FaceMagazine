<?php
require_once 'Component.php';

class H1 extends Component {
    function renderHTML() {
        echo "<h1>{$this->attr['text']}</h1>\n";
    }
}
