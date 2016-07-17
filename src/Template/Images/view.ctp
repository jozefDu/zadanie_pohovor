<!-- File: src/Template/Articles/view.ctp -->
<h1>Nazov obrazku: <?= h($image->photo) ?></h1>

<?php 

echo $this->Html->image('avatar/' . $image->photo, array('alt' => $image->photo, 'fullBase' => true)); 

?>

<p>lokalizacia obrazku: <?= h($image->photo_dir) ?></p>
<p>lokalizacia left: <?= h($image->left_position) ?></p>
<p>lokalizacia top: <?= h($image->top_position) ?></p>
<p>Šírka: <?= h($image->Šírka) ?></p>
<p>Výška: <?= h($image->Výška) ?></p>
<p>Cas ulozenia:<small>Created: <?= $image->created->format(DATE_RFC850) ?></small></p>
