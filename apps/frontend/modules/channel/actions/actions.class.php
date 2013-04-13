<?php

/**
 * channel actions.
 *
 * @package    reader
 * @subpackage channel
 * @author     BRIGADA
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class channelActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->channels = Doctrine_Core::getTable('Channel')
      ->createQuery('a')
      ->execute();
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->channel = Doctrine_Core::getTable('Channel')->find(array($request->getParameter('id')));
    $this->forward404Unless($this->channel);
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new ChannelForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new ChannelForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($channel = Doctrine_Core::getTable('Channel')->find(array($request->getParameter('id'))), sprintf('Object channel does not exist (%s).', $request->getParameter('id')));
    $this->form = new ChannelForm($channel);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($channel = Doctrine_Core::getTable('Channel')->find(array($request->getParameter('id'))), sprintf('Object channel does not exist (%s).', $request->getParameter('id')));
    $this->form = new ChannelForm($channel);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($channel = Doctrine_Core::getTable('Channel')->find(array($request->getParameter('id'))), sprintf('Object channel does not exist (%s).', $request->getParameter('id')));
    $channel->delete();

    $this->redirect('channel/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $channel = $form->save();

      $this->redirect('channel/edit?id='.$channel->getId());
    }
  }
}
