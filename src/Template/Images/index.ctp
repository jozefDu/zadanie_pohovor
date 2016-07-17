<!-- File: src/Template/Images/index.ctp -->

<h1>zadajte svoje query: </h1>
<?php
echo $this->Form->create(null, ['url' => 'http://localhost/zadanie_praca/images/query']); 
echo $this->Form->textarea('query', ['rows' => '5', 'cols' => '5']); 
echo $this->Form->button(__('send query'));
echo $this->Form->end();
?>
<h1>Images</h1>

<h2>Pridaj obrazok</h2>
<p><?= $this->Html->link("Add Images", ['action' => 'add']) ?></p>

<h2>nahlad obrazkov</h2>
<?php foreach ($images as $image): ?>
<?php 

echo $this->Html->image('thubnails/' . $image->photo, array('alt' => $image->photo, 'fullBase' => true)); 

?>

<?php endforeach; ?>

<table>
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Dir</th>
	<th>created</th>
    </tr>

    <!-- Here is where we iterate through our $Images query object, printing out image info -->

    <?php foreach ($images as $image): ?>
    <tr>
        <td><?= $image->id ?></td>
        <td><?= $this->Html->link($image->photo, ['action' => 'view', $image->id]) ?></td>
	<td><?= $image->photo_dir ?></td>
        <td><?= $image->created->format(DATE_RFC850) ?></td>
    </tr>
    <?php endforeach; ?>
</table>


