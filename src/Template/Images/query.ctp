<h1>Vysledok dopytu</h1>

<?php foreach ($images as $image): ?>

<?php 
echo $this->Html->image('thubnails/' . $image->photo, array('alt' => $image->photo, 'fullBase' => true)); 
?>

<?php endforeach; ?>
