<?php
namespace dal;

class DAL {

	protected $registry = array();
	protected $connection;

	public function __construct($connection=NULL) {

		if ($connection == NULL) {
			throw new \Exception('DB Connection not given.');
		}

		$this->connection = $connection;
	}

	public function __set($name, $resolver) {

		$this->registry[$name] = $resolver;
	}

	// returns a closure for 
	public function __get($name) {

		if (isset($this->registry[$name]))
			return $this->registry[$name]();
		else
			return $this->$name;
	}

	public function getCQuery($name) {

		if ($this->getRegistryCount() == 0)
			throw new \Exception('Files have not been loaded');

		if (!isset($this->registry[$name]))
			throw new \Exception('This index ('. $name .') does not exist');

		$thing = $this->registry[$name];
		return $thing($this->connection);
	}

	public function addCustomQuery($query) {

		if (get_class($query) != 'CustomSelectQuery')
			throw new \Exception('Not the correct class: must be CustomSelectQuery');

	}

	/*
	 * Registers a custom query with DAL
	 */
	public function register($query, $query_things) {

		$this->$query_things['name'] = function($con=NULL) use ($query, $query_things) {
			
			$m = new DBQuery();
			$m->setValues(array(
				'query' => $query->prepareText(),
				'test_query' => NULL,
				'params' => $query_things['params'],
				'param_types' => $query_things['param_types'],
				'nullable' => $query_things['nullable'],
				'returning' => $query_things['returning'],
				'returning_value' => $query_things['returning_value'],
				'returning_assoc' => $query_things['returning_assoc'],
				'returning_list' => $query_things['returning_list'],
				'returning_class' => $query_things['returning_class'],
				'returning_cols' => $query_things['returning_cols']
			));

			$m->setConnection($con);

			return $m;
		};
	}

	public function loadFiles()
	{
		include_once('reg-test.php');
		\registerTest($this);

		include_once('reg-user.php');
		\registerUser($this);

		include_once('reg-network.php');
		\registerNetwork($this);

		include_once('reg-searchable.php');
		\registerSearchable($this);

		include_once('reg-conversation.php');
		\registerConversation($this);

		include_once('reg-image.php');
		\registerImage($this);

		include_once('reg-post.php');
		\registerPost($this);

		include_once('reg-event.php');
		\registerEvent($this);

		include_once('reg-meta.php');
		\registerMeta($this);

		include_once('reg-tweet.php');
		\registerTweet($this);
	}

	public function getRegistryCount() {
		return count($this->registry);
	}

	public function lastInsertId() {
		return $this->connection->lastInsertId();
	}

	/*
	private static $folders = array('meta', 'user', 'network', 'location',
		'language');

	public static function findOp($opId) {

		$files = self::getClassList();

		if ($files[$opId] == NULL)
			throw Exception('That query couldn\'t be found.');

		return new $files[$opId]();
	}

	private function getClassList() {

		// scan this directory
		$dir_head = __DIR__.DIRECTORY_SEPARATOR;
		$files = array();

		foreach (self::$folders as $folder) {
			$dirToScan = $dir_head . $folder;
			$scanfiles = scandir($dirToScan);

			// push to files
			foreach ($scanfiles as $f) {

				// skip buffers
				if (stripos($f, '~'))
					continue;

				// skip periods
				if (stripos($f, '.') == 0)
					continue;

				echo $folder. ' ';
				echo $f;
				echo "\n";

				$length = strlen($f);
				$f_id = substr($f, 0, $length-4); // minus offset (1) + len(.php)
				$files[$f_id] = 'dal' .'\\'. $folder . '\\' . $f_id;
			}
		}

		return $files;
	}
	 */
}

?>
