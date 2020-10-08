<?php

script('assembly', 'videocall');
style('assembly', 'videocall');

if ($time) {
?>
Começa às <?php echo $time; ?>, aguarde até a hora marcada:

<p id="demo"></p><?php

} else {
    echo 'Sem reuniões agendadas';
}?>