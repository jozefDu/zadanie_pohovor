<h1>Add Images</h1>
<?php
    echo $this->Form->create($image, ['type' => 'file', 'url' => 'http://localhost/zadanie_praca/index.php/images/add']); // Dont miss this out or no files will upload
    echo $this->Form->input('photo', ['type' => 'file']);
    echo $this->Form->input('left_position');
    echo $this->Form->input('top_position');
    echo $this->Form->input('Šírka');
    echo $this->Form->input('Výška');
    echo $this->Form->button(__('Save image'));
    echo $this->Form->hidden('photo_dir');
    echo $this->Form->hidden('time');
    echo $this->Form->end();
?>
