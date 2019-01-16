<?php

namespace Drupal\otsuka_signal_dmd_aim\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;

/**
 * {@inheritdoc}
 */
class OtsukaSignalDmdAimSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'otsuka_signal_dmd_aim_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['otsuka_signal_dmd_aim.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('otsuka_signal_dmd_aim.settings');

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

    $form['snippet_location']=[
      '#type' => 'radios',
      '#title' => $this->t('Snippet Location'),
      '#description' => $this->t('Location of the code snippet on the page'),
      '#id' => 'dmd-aim-loc',
      '#options' => [
        'top' => $this->t('Top of page'),
        'bottom'  => $this->t('Bottom of page'),
      ],
      '#empty_option' => $this->t('- Select -'),
      '#default_value' => $config->get('snippet_location'),
    ];

    $form['aim'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('DMD AIM settings, only applied when AIM site ID is provided'),
    ];

    $form['aim']['site_id'] = [
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

    

    $form['aim']['aim_source_mode'] = [
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

    $form['#attached']['library'][] = 'otsuka_signal_dmd_aim/otsuka_signal_dmd_aim';

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
    $form_state->setValue('snippet_location', Html::escape($form_state->getValue('snippet_location')));

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('otsuka_signal_dmd_aim.settings')
      ->set('signal_site_id', $form_state->getValue('signal_site_id'))
      ->set('aim_source_mode', $form_state->getValue('aim_source_mode'))
      ->set('site_id', $form_state->getValue('site_id'))
      ->set('snippet_location', $form_state->getValue('snippet_location'))
      ->save();
  }

}
