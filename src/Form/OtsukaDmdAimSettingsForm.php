<?php

namespace Drupal\otsuka_dmd_aim\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;

/**
 * {@inheritdoc}
 */
class OtsukaDmdAimSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'otsuka_dmd_aim_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['otsuka_dmd_aim.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('otsuka_dmd_aim.settings');

    $form['signal_site_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Signal Site ID'),
      '#description' => $this->t('The ID assigned by Otsuka Signal Manager for this website container.'),
      '#default_value' => $config->get('signal_site_id'),
      '#attributes' => ['placeholder' => ['xxxxxxx']],
      '#size' => 12,
      '#maxlength' => 15,
      '#required' => FALSE,
    ];

    $defaultAimReadyFunction =
    'AIM.ready(function(){
    // Fetch DMD Javascript Session_ID within cookie value "dmd-sid"
    var aim_session_id = document.cookie.replace(/(?:(?:^|.*;\s*)dmd-sid\s*\=\s*([^;]*).*$)|^.*$/,"$1") || false;
    
    s.linkTrackVars = "eVar01";
    s.eVar01 = aim_session_id;
    s.tl(true, "o", s.pageName + "|" + s.eVar01);
});';

    $form['aim_source_mode'] = [
      '#type' => 'radios',
      '#title' => $this->t('AIM Source Mode'),
      '#description' => $this->t('UAT or Production Mode?'),
      '#id' => 'dmd-aim-id',
      '#options' => [
        'uat' => $this
          ->t('UAT: uat.medtargetsystem.com'),
        'www' => $this
          ->t('Prod: www.medtargetsystem.com'),
      ],
      '#empty_option' => $this->t('- Select -'),
      '#default_value' => $config->get('aim_source_mode'),
    ];

    $form['site_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('AIM Site ID'),
      '#description' => $this->t('The ID assigned by Otsuka DMD AIM Manager.'),
      '#default_value' => $config->get('site_id'),
      '#id' => 'dmd-aim-id',
      '#attributes' => ['placeholder' => ['XX-000-D2EE2A91']],
      '#size' => 20,
      '#maxlength' => 20,
      '#required' => FALSE,
    ];

    $form['aim_ready_function'] = [
      '#type' => 'textarea',
      '#title' => $this->t('AIM.ready() function'),
      '#description' => $this->t('The AIM.ready() function can be used to access the visitor’s JavaScript Session ID, located within the 1st party cookie dmd-sid. This function should be implemented immediately after the function AIM.init(<API KEY>), and can be expanded to include the DGID from the AIM Signal output as well.'),
      '#default_value' => $config->get('aim_ready_function') ? $config->get('aim_ready_function') : $defaultAimReadyFunction,
      '#id' => 'dmd-aim-ready',
      '#rows' => 10,
      '#required' => FALSE,
    ];

    $form['#attached']['library'][] = 'otsuka_dmd_aim/otsuka_dmd_aim';

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Trim the text values.
    $form_state->setValue('signal_site_id', Html::escape($form_state->getValue('signal_site_id')));
    $form_state->setValue('aim_source_mode', Html::escape($form_state->getValue('aim_source_mode')));
    $form_state->setValue('site_id', Html::escape($form_state->getValue('site_id')));
    $form_state->setValue('aim_ready_function', $form_state->getValue('aim_ready_function'));

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('otsuka_dmd_aim.settings')
      ->set('signal_site_id', $form_state->getValue('signal_site_id'))
      ->set('aim_source_mode', $form_state->getValue('aim_source_mode'))
      ->set('site_id', $form_state->getValue('site_id'))
      ->set('aim_ready_function', $form_state->getValue('aim_ready_function'))
      ->save();
  }

}
