<?php

script('assembly', 'videocall');
style('assembly', 'videocall');

if ($time) {
?>
Aguarde até a hora marcada: <?php echo $time;
} else {
    echo 'Sem reuniões agendadas';
}?>