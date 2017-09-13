<?php  
/**  
 * @file  
 * Contains Drupal\idtlabs\Form\ServiceForm.  
 */  
namespace Drupal\idtlabs\Form;  
use Drupal\Core\Form\ConfigFormBase;  
use Drupal\Core\Form\FormStateInterface; 
use Drupal\Core\Routing\RouteProviderInterface;

class ServiceForm extends ConfigFormBase {  
/**  
   * {@inheritdoc}  
   */  
  protected function getEditableConfigNames() {  
    return [  
      'service.adminsettings',  
    ];  
  }  

  /**  
   * {@inheritdoc}  
   */  
  public function getFormId() {  
    return 'service_form';  
  }


  /**  
   * {@inheritdoc}  
   */  
	  public function buildForm(array $form, FormStateInterface $form_state) {  
	    $config = $this->config('service.adminsettings');  

	    $form['welcome_message'] = [  
			'#type' => 'textarea',  
			'#title' => $this->t('Welcome message'),  
			'#description' => $this->t('Welcome message display to users when they login'),  
			'#default_value' => $config->get('welcome_message'),
			'#required' => TRUE
	    ];  

	    $form['message_path'] = [  
			'#type' => 'textfield',  
			'#title' => $this->t('Welcome message display path'),  
			'#description' => $this->t('Welcome message display to specific path'),  
			'#default_value' => $config->get('message_path'), 
			'#required' => TRUE,
			'#maxlength' => 150,
			'#size' => 45,
	    ];  

    	/*Load all drupal roles*/

	 	$roles = array_map(array('\Drupal\Component\Utility\Html', 'escape'), user_role_names(FALSE));

		$form['user_roles'] = array(
			'#type' => 'checkboxes',
			'#title' => $this->t('Roles'),
			'#default_value' =>  $config->get('user_roles'),
			'#options' => $roles,
			'#required' => TRUE
		);
		$form['display_message_type'] = array(
			'#type' => 'radios',
			'#title' => $this->t('Select Display Message Type'),
			'#default_value' =>  $config->get('display_message_type'),
			'#options' =>  [
			    'status' 	=> $this->t('Status'),
			    'warning' 	=> $this->t('Warning'),
			    'error' 	=> $this->t('Error')
			],
			'#required' => TRUE
		);
	    return parent::buildForm($form, $form_state);  
	  } 

	    /**
   * {@inheritdoc}
   */
  	public function validateForm(array &$form, FormStateInterface $form_state) {
  		/*check path is exit in route table*/

  		$route = \Drupal::service('path.validator')->isValid($form_state->getValue('message_path'));
		
		if (!$route) {
	      	$form_state->setErrorByName('message_path', $this->t('Path is invalid. Please enter valid path.'));
	    }
	    
	    if(substr($route , -1)=='/'){
    	  $form_state->setErrorByName('message_path', $this->t('Path cannot end with trailing slash. Please enter valid path.'));
	   	}
	  }

		/**  
		* {@inheritdoc}  
		*/ 
	public function submitForm(array &$form, FormStateInterface $form_state) {  
		parent::submitForm($form, $form_state);  

		$this->config('service.adminsettings')  
		  ->set('welcome_message', $form_state->getValue('welcome_message'))  
  		  ->set('message_path', $form_state->getValue('message_path'))  
  		  ->set('user_roles', $form_state->getValue('user_roles'))  
  		  ->set('display_message_type', $form_state->getValue('display_message_type'))  
		->save();  
	}  
}  
