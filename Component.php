<?php

class Component {
    public $children = [];
    public $attr = [];

    function getAttr($name) {
        return $attr['name'];
    }

    function __construct($children = [], $attr = []) {
        $this->children = $children;
        $this->attr = $attr;
    }

    function renderHTML() {
        foreach ($this->children as $child) {
            $child->renderHTML();
        }
    }
}
