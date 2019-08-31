<?php
require_once 'Component.php';

class AccountForm extends Component {
    function __construct($children = [], $attribs = []) {
        parent::__construct($children, $attribs);
        foreach (
        ['action', 'inline', 'email', 'pw', 'fname', 'scrname', 'dob',
            'gender', 'vis', 'loc', 'stat'] as $attr)
        {
            if (!isset($attribs[$attr])) {
                $this->attr[$attr] = "";
            }
        }
        if (!isset($attribs['omit'])) {
            $this->attr['omit'] = [];
        }
    }
    function renderHTML() {
        $formClass = $this->attr['inline'] ? "" : "form-box";
        $cm = $this->attr['gender'] == "Male" ? "checked" : "";
        $cf = $this->attr['gender'] == "Female" ? "checked" : "";
        $c0 = $this->attr['vis'] == 0 ? "checked" : "";
        $c1 = $this->attr['vis'] == 1 ? "checked" : "";
        $c2 = $this->attr['vis'] == 2 ? "checked" : "";

        echo <<<EOT
        <form class="{$formClass}" method="post" action="{$this->attr['action']}">
EOT;
        if (!isset($this->attr['omit']['email'])) {
            echo <<<EOT
            <div>
            <label class="formLabel" for="email">Email Address</label>
            <input type="email" name="email" value="{$this->attr['email']}"
            required="required"/>
            </div>
EOT;
        }
        if (!isset($this->attr['omit']['pw'])) {
            echo <<<EOT
            <div>
            <label class="formLabel" for="pw">Password</label>
            <input type="password" name="pw" value="{$this->attr['pw']}"
            required="required"/>
            </div>
EOT;
        }
        if (!isset($this->attr['omit']['fname'])) {
            echo <<<EOT
            <div>
            <label class="formLabel" for="fname">Full name</label>
            <input type="text" name="fname" value="{$this->attr['fname']}"
            required="required"/>
            </div>
EOT;
        }
        if (!isset($this->attr['omit']['scrname'])) {
            echo <<<EOT
            <div>
            <label class="formLabel" for="scrname">Screen name</label>
            <input type="text" name="scrname" value="{$this->attr['scrname']}"
            required="required"/>
            </div>
EOT;
        }
        if (!isset($this->attr['omit']['dob'])) {
            echo <<<EOT
            <div>
            <label class="formLabel" for="dob">Date of Birth</label>
            <input type="date" name="dob" value="{$this->attr['dob']}"
            required="required"/>
            </div>
EOT;
        }
        if (!isset($this->attr['omit']['gender'])) {
            echo <<<EOT
            <div>
            <label class="formLabel" for="gender">Gender</label>
            <label class="formLabel" for="gender">Male</label>
            <input type="radio" name="gender" value="Male"/ {$cm}>  
            <label class="formLabel" for="gender">Female</label>
            <input type="radio" name="gender" value="Female" {$cf}/>
            </div>
EOT;
        }
        if (!isset($this->attr['omit']['vis'])) {
            echo <<<EOT
            <div>
            <label class="formlabel" for="vis">Visibility</label>
            <label class="formlabel" for="vis">Everyone</label>
            <input type="radio" name="vis" value="0" {$c0}/>
            <label class="formlabel" for="vis">Friends-only</label>
            <input type="radio" name="vis" value="1" {$c1}/>
            <label class="formlabel" for="vis">Private</label>
            <input type="radio" name="vis" value="2" {$c2}/>
            </div>
EOT;
        }

        if (!isset($this->attr['omit']['vis'])) {
            echo <<<EOT
            <div>
            <label class="formlabel" for="location">Your current location</label>
            <input type="text" name="location" value="{$this->attr['loc']}"/>
            </div>
EOT;
        }
        if (!isset($this->attr['omit']['vis'])) {
            echo <<<EOT
            <div>
            <label class="formlabel" for="location">Your current status</label>
            <textarea class="status" name="status">{$this->attr['stat']}</textarea>
            </div>
EOT;
        }
        if (!isset($this->attr['nosubmit'])) {
            echo <<<EOT
                <input value="{$this->attr['verb']}" type="submit"/>
EOT;
        }

        echo '</form>';
    }
}
