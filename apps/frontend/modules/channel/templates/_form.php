<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<form action="<?php echo url_for('channel/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields(false) ?>
          &nbsp;<a href="<?php echo url_for('channel/index') ?>">Back to list</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'channel/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['url']->renderLabel() ?></th>
        <td>
          <?php echo $form['url']->renderError() ?>
          <?php echo $form['url'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['title']->renderLabel() ?></th>
        <td>
          <?php echo $form['title']->renderError() ?>
          <?php echo $form['title'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['link']->renderLabel() ?></th>
        <td>
          <?php echo $form['link']->renderError() ?>
          <?php echo $form['link'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['description']->renderLabel() ?></th>
        <td>
          <?php echo $form['description']->renderError() ?>
          <?php echo $form['description'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['language']->renderLabel() ?></th>
        <td>
          <?php echo $form['language']->renderError() ?>
          <?php echo $form['language'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['copyright']->renderLabel() ?></th>
        <td>
          <?php echo $form['copyright']->renderError() ?>
          <?php echo $form['copyright'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['editor']->renderLabel() ?></th>
        <td>
          <?php echo $form['editor']->renderError() ?>
          <?php echo $form['editor'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['webmaster']->renderLabel() ?></th>
        <td>
          <?php echo $form['webmaster']->renderError() ?>
          <?php echo $form['webmaster'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['ttl']->renderLabel() ?></th>
        <td>
          <?php echo $form['ttl']->renderError() ?>
          <?php echo $form['ttl'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['logo_url']->renderLabel() ?></th>
        <td>
          <?php echo $form['logo_url']->renderError() ?>
          <?php echo $form['logo_url'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['logo_width']->renderLabel() ?></th>
        <td>
          <?php echo $form['logo_width']->renderError() ?>
          <?php echo $form['logo_width'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['logo_height']->renderLabel() ?></th>
        <td>
          <?php echo $form['logo_height']->renderError() ?>
          <?php echo $form['logo_height'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['created_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['created_at']->renderError() ?>
          <?php echo $form['created_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['updated_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['updated_at']->renderError() ?>
          <?php echo $form['updated_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['categories_list']->renderLabel() ?></th>
        <td>
          <?php echo $form['categories_list']->renderError() ?>
          <?php echo $form['categories_list'] ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
