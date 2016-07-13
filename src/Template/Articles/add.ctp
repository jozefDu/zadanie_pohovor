<h1>Add Images</h1>
<?php
    echo $this->Form->create($images, ['type' => 'file']); // Dont miss this out or no files will upload
    echo $this->Form->input('title');
    echo $this->Form->input('body', ['rows' => '3']);
    echo $this->Form->button(__('Save Article'));
    echo $this->Form->end();
?>
