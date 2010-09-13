<?php
/**
 * RedBean Links
 * @file                RedBean/LinkManager.php
 * @description		Manages foreign keys
 *
 * @author			Gabor de Mooij
 * @license			BSD
 *
 * (c) G.J.G.T. (Gabor) de Mooij
 * This source file is subject to the BSD/GPLv2 License that is bundled
 * with this source code in the file license.txt.
 */
class RedBean_LinkManager extends RedBean_CompatManager {

	/**
	 * Specify what database systems are supported by this class.
	 * @var array $databaseSpecs
	 */
	protected $supportedSystems = array(
			  RedBean_CompatManager::C_SYSTEM_MYSQL => "5",
			  RedBean_CompatManager::C_SYSTEM_SQLITE=>"3",
			  RedBean_CompatManager::C_SYSTEM_POSTGRESQL=>"8"
	);

	/**
	 * @var RedBean_OODB
	 */
	protected $oodb;

	/**
	 * @var RedBean_Adapter_DBAdapter
	 */
	protected $adapter;

	/**
	 * @var RedBean_QueryWriter
	 */
	protected $writer;


	/**
	 * Constructor
	 * @param RedBean_ToolBox $tools
	 */
	public function __construct( RedBean_ToolBox $tools ) {
		$this->oodb = $tools->getRedBean();
		$this->adapter = $tools->getDatabaseAdapter();
		$this->writer = $tools->getWriter();
	}

	/**
	 * Returns the fieldname for a foreign key.
	 * @param string $typeName
	 * @return string $fieldName
	 */
	public function getLinkField( $typeName ) {
		$fieldName = strtolower( $typeName )."_id";
		$fieldName = preg_replace( "/\W/","", $fieldName );
		return $fieldName;
	}

	/**
	 * Adds a reference to bean2 in bean1.
	 * @param RedBean_OODBBean $bean1
	 * @param RedBean_OODBBean $bean2
	 */
	public function link(RedBean_OODBBean $bean1, RedBean_OODBBean $bean2) {
		if (!$bean2->id) {
			//Not saved, then we have no ID, so first save!
			$this->oodb->store( $bean2 );
		}
		$fieldName = $this->getLinkField( $bean2->getMeta("type") );
		$bean1->$fieldName = $bean2->id;
		return $this;
	}

	/**
	 * Returns a linked bean.
	 * @param RedBean_OODBBean $bean
	 * @param string $typeName
	 * @return RedBean_OODBBean $bean
	 */
	public function getBean( RedBean_OODBBean $bean, $typeName ) {
		$fieldName = $this->getLinkField($typeName);
		$id = (int)$bean->$fieldName;
		if ($id) {
			return $this->oodb->load($typeName, $id);
		}
		else {
			return $this->oodb->dispense($typeName);
		}
	}

	/**
	 * Removes a linked bean.
	 * @param RedBean_OODBBean $bean
	 * @param string $typeName
	 */
	public function breakLink( RedBean_OODBBean $bean, $typeName ) {
		$fieldName = $this->getLinkField($typeName);
		$bean->$fieldName = NULL;
	}


	/**
	 * Returns a linked bean ID.
	 * @param RedBean_OODBBean $bean
	 * @param string $typeName
	 * @return RedBean_OODB $bean
	 */
	public function getKey(RedBean_OODBBean $bean, $typeName) {
		$fieldName = $this->getLinkField($typeName);
		$id = (int)$bean->$fieldName;
		return $id;
	}

}