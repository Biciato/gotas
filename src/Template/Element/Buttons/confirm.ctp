<div class="form-group row">
    <div class="col-lg-12">
        <?= $this->Form->button(
            __('{0} {1}', $this->Html->tag('i', '', ['class' => 'fa fa-save']), $titleButton),
            [
                'class' => 'btn btn-primary',
                'escape' => false
                ]
        ) ?>
    </div>
</div>
