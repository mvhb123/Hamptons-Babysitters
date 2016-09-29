<?php

class Hb_View_Helper_Blog extends Zend_View_Helper_Abstract {

    public function blog($dob) {

        try {
            $channel = new Zend_Feed_Rss('http://www.hamptonsbabysitters.com/feed');
            //echo '<pre>';
            //print_r($channel);


            $bodyTxt = '<div class="panel-body">
                        <section id="flip-scroll">
                            <table class="table table-bordered table-striped table-condensed cf">
                                <thead class="cf">
   
                         
                        </thead>
                        <tbody>';

            foreach ($channel as $item) {
                //	echo $item->title() . "\n";
                //echo $item->link() . "\n";

                $t.= '<tr >
                            
                            <th><a title="' . $item->title() . '"  href="' . $item->link() . '">' . $item->title() . '</a></th>
                            
                          </tr>';
            }
            $bodyTxt.=$t . '</tbody>
                      </table>
                    </div>
                  </div>
            </div>';


            echo $bodyTxt;
        } catch (Exception $e) {
            
        }
    }

}
