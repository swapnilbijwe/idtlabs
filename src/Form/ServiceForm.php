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
			'#description' => $this->t('Welcome message display to specific path. Please add url start with slash'),  
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
    	$alias = rtrim(trim($form_state->getValue('message_path')), " \\/");
    
	      // Validate that the submitted alias does not exist yet.
  		$is_exists = \Drupal::service('path.validator')->isValid($alias);
	    
	    if (!$is_exists) {
	        $form_state->setErrorByName('message_path', $this->t('Path not found. Please enter valid path.'));
	    }
        if ($alias && $alias[0] !== '/') {
      		$form_state->setErrorByName('message_path', $this->t('The alias needs to start with a slash.'));
    	}
	  }

		/**  
		* {@inheritdoc}  
		*/ 
	public function submitForm(array &$form, FormStateInterface $form_state) {  
		parent::submitForm($form, $form_state);  
		
		$message_path_alias = \Drupal::service('path.alias_manager')->getAliasByPath($form_state->getValue('message_path'));
		$message_path_alias = rtrim(trim($message_path_alias), " \\/");

		$this->config('service.adminsettings')  
		  ->set('welcome_message', $form_state->getValue('welcome_message'))  
  		  ->set('message_path', $message_path_alias)  
  		  ->set('user_roles', $form_state->getValue('user_roles'))  
  		  ->set('display_message_type', $form_state->getValue('display_message_type'))  
		->save();  
	}  
}  
