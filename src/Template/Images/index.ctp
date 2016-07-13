<!-- File: src/Template/Images/index.ctp -->

<p><?= $this->Html->link("Add Images", ['action' => 'add']) ?></p>
<?php echo $this->Form->input('query'); ?>

<h1>Images</h1>
<table>
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Dir</th>
	<th>last change</th>
    </tr>

    <!-- Here is where we iterate through our $Images query object, printing out image info -->

    <?php foreach ($images as $image): ?>
    <tr>
        <td><?= $image->id ?></td>
        <td><?= $this->Html->link($image->photo, ['action' => 'view', $image->id]) ?></td>
	<td><?= $image->photo_dir ?></td>
        <td><?= $image->time->format(DATE_RFC850) ?></td>
    </tr>
    <?php endforeach; ?>
</table>


