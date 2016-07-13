<h1>Add Article</h1>
<?php
echo $this->Form->create($entity, ['type' => 'file']); // Dont miss this out or no files will upload
echo $this->Form->input('image', ['type' => 'file']);
echo $this->Form->button(__('Submit'));
echo $this->Form->end();
