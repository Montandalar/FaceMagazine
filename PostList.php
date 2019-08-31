<?php
require_once 'Component.php';

class PostList extends Component {
    function renderHTML() {
        $this->attr['name'] = $_SESSION['email'];
        echo <<<EOT
<div id='main-content-container'>
  <div class="make-post-container">
    <form action="make_post.php" method="post">
    <p>What are you up to, {$this->attr['name']}</p>
    <textarea name="body"></textarea>
    <input value="Make post" type="submit"/>
    </form>
  </div>

  <div class="post">
  <p>Body</p>
  <!-- ... n paragraphs -->

    <p>
    Bacon ipsum dolor amet pastrami hamburger boudin, frankfurter burgdoggen turducken pork cow sausage ball tip tri-tip kielbasa ground round. Venison shank bresaola rump bacon pastrami. Filet mignon flank shoulder bacon cupim corned beef shank prosciutto drumstick frankfurter pig tail chicken ribeye burgdoggen. Burgdoggen meatball beef ribs alcatra biltong pork belly.
    </p>

    <p>
    Tri-tip biltong capicola swine bacon ham hock. Rump tongue turkey, short ribs salami buffalo picanha shankle sirloin. Kielbasa tail chuck swine. Shoulder chuck porchetta corned beef pastrami. Turducken filet mignon short ribs leberkas, drumstick capicola pancetta.
    </p>

    <p>
    Corned beef fatback strip steak tri-tip ball tip meatloaf jowl venison. Beef ribs burgdoggen meatball, sirloin venison tongue buffalo alcatra boudin andouille pork loin. Ham hock kevin buffalo picanha, andouille capicola tongue sirloin filet mignon meatball chicken venison boudin pig ground round. Kielbasa ribeye ham, brisket pork belly drumstick porchetta cow swine.
    </p>
    <p>
    Ham hock spare ribs ham kevin andouille, cupim porchetta drumstick buffalo beef ball tip doner sausage capicola prosciutto. Spare ribs salami tenderloin, ham hock ball tip landjaeger picanha meatloaf pig hamburger chicken cupim andouille. Jowl pig turducken t-bone swine pastrami, andouille venison. Cupim meatball ball tip bacon kevin turducken, andouille meatloaf doner brisket porchetta pork chop. Short ribs rump boudin pastrami drumstick buffalo jerky.
    <p>
    Kielbasa tenderloin venison shoulder kevin jowl drumstick tri-tip strip steak. Ham frankfurter ham hock beef ribs capicola. Capicola ground round ball tip boudin shankle shoulder. Pig shankle burgdoggen, short loin alcatra leberkas meatloaf ground round kielbasa tri-tip pork chop. Leberkas tenderloin tongue brisket short loin. Pork shank pork chop ball tip, pancetta meatloaf landjaeger. Landjaeger brisket jerky, ham leberkas flank hamburger picanha beef ribs ham hock.
    </p>
      <!-- ... n posts -->
    </div>
</div>

EOT;

    }
}
