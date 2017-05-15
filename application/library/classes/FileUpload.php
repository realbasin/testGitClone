<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
class FileUpload {
	/**
	 * 允许上传的文件类型. 例如: array('jpg', 'gif', 'png').
	 */
	public $allowed_file_extensions;
	/**
	 * 文件类型MIME验证
	 * array(
	 *     'jpg' => array('image/jpeg', 'image/pjpeg'),
	 *     'txt' => array('text/plain'),
	 * );
	 */
	public $file_extensions_mime_types;
	/**
	 * 文件大小，单位byte
	 */
	public $max_file_size;
	/**
	 * 保存的文件名称，不包含后缀
	 */
	public $new_file_name;
	/**
	 * 是否允许覆盖同名文件
	 */
	public $overwrite = false;
	/**
	 * 是否重命名文件
	 */
	public $web_safe_file_name = true;
	/**
	 * 是否使用安全扫描
	 */
	public $security_scan = false;
	/**
	 * 如果多文件上传有某些文件没有通过验证是否需要停止上传
	 */
	public $stop_on_failed_upload_multiple = true;
	/**
	 * 上传文件夹
	 */
	public $move_uploaded_to = '.';

	/**
	 * 错误提示
	 */
	public $error_messages = array();

	/**
	 * 上传文件名 ($_FILES['input_file_name']).
	 */
	protected $input_file_name;
	/**
	 * 使用相同上传文件名的多文件集合
	 */
	protected $files = array();
	/**
	 * 上传队列
	 */
	protected $move_uploaded_queue = array();

	/**
	 *
	 * @param 上传空间名称
	 */
	public function __construct($input_file_name = '') {
		$this -> clear();
		$this -> setInputFileName($input_file_name);
	}

	public function __destruct() {
		$this -> clear();
	}

	protected static function __($string) {
		return $string;
	}

	public function clear() {
		$this -> allowed_file_extensions = null;
		$this -> error_messages = array();
		$this -> file_extensions_mime_types = null;
		$this -> files = array();
		$this -> input_file_name = null;
		$this -> max_file_size = null;
		$this -> move_uploaded_queue = array();
		$this -> move_uploaded_to = '.';
		$this -> new_file_name = null;
		$this -> overwrite = false;
		$this -> web_safe_file_name = true;
		$this -> security_scan = false;
		$this -> stop_on_failed_upload_multiple = true;
	}

	/**
	 * 清理临时文件夹
	 */
	protected function clearUploadedAtTemp() {
		foreach ($this->move_uploaded_queue as $key => $queue_item) {
			if (is_array($queue_item) && isset($queue_item['tmp_name'])) {
				if (is_file($queue_item['tmp_name']) && is_writable($queue_item['tmp_name'])) {
					unlink($queue_item['tmp_name']);
				}
			}
		}
		unset($key, $queue_item);
		$this -> move_uploaded_queue = array();
	}

	/**
	 * 获取上传数据
	 * @return 上传成功的文件数据
	 * $output = array(
	 *     'input_file_name_key' => array(
	 *         'name' => 'file_name_where_user_selected_in_the_upload_form.ext',
	 *         'extension' => 'ext',
	 *         'size' => 'file size in bytes.',
	 *         'new_name' => 'new_file_name_that_was_set_while_upload_process.ext',
	 *         'full_path_new_name' => '/full/move_uploaded_path/to/new_file_name_that_was_set_while_upload_process.ext',
	 *         'mime' => 'The real file mime type',
	 *         'md5_file' => 'The md5 file value.',
	 *     ),
	 *     'other_input_file_name_key' => array(
	 *         'name' => '...',
	 *         'extension' => '...',
	 *         'size' => '...',
	 *         'new_name' => '...',
	 *         'full_path_new_name' => '...',
	 *         'mime' => '...',
	 *         'md5_file' => '...',
	 *     ),
	 * );
	 * 如果上传失败，返回空值
	 */
	public function getUploadedData() {
		if (empty($this -> move_uploaded_queue) || !is_array($this -> move_uploaded_queue)) {
			return array();
		}

		$output = array();

		foreach ($this->move_uploaded_queue as $key => $queue_item) {
			if (is_array($queue_item) && array_key_exists('name', $queue_item) && array_key_exists('tmp_name', $queue_item) && array_key_exists('new_name', $queue_item) && array_key_exists('move_uploaded_to', $queue_item) && array_key_exists('move_uploaded_status', $queue_item) && $queue_item['move_uploaded_status'] === 'success') {
				$file_name_explode = explode('.', $queue_item['name']);
				$file_extension = (isset($file_name_explode[count($file_name_explode) - 1]) ? $file_name_explode[count($file_name_explode) - 1] : null);
				unset($file_name_explode);

				$Finfo = new finfo();
				$mime = $Finfo -> file($queue_item['move_uploaded_to'], FILEINFO_MIME_TYPE);
				unset($Finfo);

				$output[$key] = array();
				$output[$key]['name'] = $queue_item['name'];
				$output[$key]['extension'] = $file_extension;
				$output[$key]['size'] = (is_file($queue_item['move_uploaded_to']) ? filesize($queue_item['move_uploaded_to']) : 0);
				$output[$key]['new_name'] = $queue_item['new_name'];
				$output[$key]['full_path_new_name'] = $queue_item['move_uploaded_to'];
				$output[$key]['mime'] = $mime;
				$output[$key]['md5_file'] = (is_file($queue_item['move_uploaded_to']) ? md5_file($queue_item['move_uploaded_to']) : null);

				unset($file_extension, $mime);
			}
		}

		return $output;
	}

	/*
	 * 获取错误信息
	 */
	public function getErrorMessage() {
		return $this -> error_messages;
	}

	/**
	 * 移动上传文件
	 *
	 * @return boolean
	 */
	protected function moveUploadedFiles() {
		$i = 0;
		if (is_array($this -> move_uploaded_queue)) {
			foreach ($this->move_uploaded_queue as $key => $queue_item) {
				if (is_array($queue_item) && isset($queue_item['name']) && isset($queue_item['tmp_name']) && isset($queue_item['new_name'])) {
					$destination_name = $queue_item['new_name'];

					if ($this -> overwrite === false) {
						// 检查是否有同名文件
						$destination_name = $this -> renameDuplicateFile($destination_name);
					}

					$move_result = move_uploaded_file($queue_item['tmp_name'], $this -> move_uploaded_to . DIRECTORY_SEPARATOR . $destination_name);
					if ($move_result === true) {
						$this -> move_uploaded_queue[$key] = array_merge($this -> move_uploaded_queue[$key], array('new_name' => $destination_name, 'move_uploaded_status' => 'success', 'move_uploaded_to' => $this -> move_uploaded_to . DIRECTORY_SEPARATOR . $destination_name, ));
						$i++;
					} else {
						$this -> error_messages = array_merge($this -> error_messages, array(sprintf(static::__(\Core::L('file_move_fail') . '(%s =&gt; %s)'), $queue_item['name'], $this -> move_uploaded_to . DIRECTORY_SEPARATOR . $destination_name)));
					}

					unset($destination_name, $move_result);
				}
			}
			unset($key, $queue_item);
		}

		if ($i == count($this -> move_uploaded_queue) && $i > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 如果文件存在则重命名
	 */
	protected function renameDuplicateFile($file_name, $loop_count = 1) {
		if (!file_exists($this -> move_uploaded_to . DIRECTORY_SEPARATOR . $file_name)) {
			return $file_name;
		} else {
			$file_name_explode = explode('.', $file_name);
			$file_extension = (isset($file_name_explode[count($file_name_explode) - 1]) ? $file_name_explode[count($file_name_explode) - 1] : null);
			unset($file_name_explode[count($file_name_explode) - 1]);
			$file_name_only = implode('.', $file_name_explode);
			unset($file_name_explode);

			$i = 1;
			$found = true;
			do {
				$new_file_name = $file_name_only . '_' . $i . '.' . $file_extension;
				if (file_exists($this -> move_uploaded_to . DIRECTORY_SEPARATOR . $new_file_name)) {
					$found = true;
					if ($i > 1000) {
						$file_name = uniqid() . '-' . str_replace('.', '', microtime(true));
						$found = false;
					}
				} else {
					$file_name = $new_file_name;
					$found = false;
				}
				$i++;
			} while ($found === true);

			unset($file_extension, $file_name_only, $new_file_name);
			return $file_name;
		}
	}

	/**
	 * 安全检测
	 *
	 * @return boolean
	 */
	protected function securityScan() {
		if (is_array($this -> files[$this -> input_file_name]) && array_key_exists('name', $this -> files[$this -> input_file_name]) && array_key_exists('tmp_name', $this -> files[$this -> input_file_name]) && $this -> files[$this -> input_file_name]['tmp_name'] != null) {
			if (is_file($this -> files[$this -> input_file_name]['tmp_name'])) {
				$file_content = file_get_contents($this -> files[$this -> input_file_name]['tmp_name']);
				// 检查PHP标记
				if (strpos($file_content, '<?php') !== false) {
					$this -> error_messages = array_merge($this -> error_messages, array(sprintf(static::__(\Core::L('file_scan_fail') . '(%s).'), $this -> files[$this -> input_file_name]['name'])));
					return false;
				}

				if (strpos($file_content, '#!/') !== false && strpos($file_content, '/perl') !== false) {
					// found cgi/perl header.
					$this -> error_messages = array_merge($this -> error_messages, array(sprintf(static::__(\Core::L('file_scan_fail') . '(%s).'), $this -> files[$this -> input_file_name]['name'])));
					return false;
				}

				unset($file_content);
			}
		}

		return true;
	}

	/**
	 * 设置上传文件
	 */
	public function setInputFileName($input_file_name) {
		$this -> input_file_name = $input_file_name;
	}

	/*
	 * 设置保存路径
	 */
	public function setSavePath($path) {
		$this -> move_uploaded_to = $path;
	}

	/*
	 * 允许上传的文件后缀名集合
	 */
	public function setExtensions(Array $ext) {
		$this -> allowed_file_extensions = $ext;
	}

	/*
	 * 最大允许上传的文件大小
	 */
	public function setFileSize($filesize) {
		$this -> max_file_size = $filesize;
	}

	/*
	 * 保存后的文件名称，不要后缀
	 */
	public function setFileName($filename) {
		$this -> new_file_name = $filename;
	}

	/*
	 * 是否允许覆盖
	 */
	public function setOverwrite($overwrite) {
		$this -> overwrite = $overwrite;
	}

	/*
	 * 是否设置安全名称
	 */
	public function setSafeName($bsafe) {
		$this -> web_safe_file_name = $bsafe;
	}

	/*
	 * 是否启用安全扫描
	 */
	public function setSecurity($scan) {
		$this -> security_scan = $scan;
	}

	/*
	 * 多文件上传，如果有任意文件错误则停止
	 * false将跳过上传失败的文件
	 */
	public function setFailedMultiple($fail) {
		$this -> failed_upload_multiple = $fail;
	}

	/**
	 * 如果未设置文件名称则自动设置
	 */
	protected function setNewFileName() {
		$this -> new_file_name = trim($this -> new_file_name);

		if ($this -> new_file_name == null) {
			if (is_array($this -> files[$this -> input_file_name]) && array_key_exists('name', $this -> files[$this -> input_file_name])) {
				$file_name_explode = explode('.', $this -> files[$this -> input_file_name]['name']);
				unset($file_name_explode[count($file_name_explode) - 1]);
				$this -> new_file_name = implode('.', $file_name_explode);
				unset($file_name_explode);
			} else {
				$this -> setNewFileNameToRandom();
			}
		}
		$reserved_characters = array('\\', '/', '?', '%', '*', ':', '|', '"', '<', '>', '!', '@');
		$this -> new_file_name = str_replace($reserved_characters, '', $this -> new_file_name);
		unset($reserved_characters);

		if (preg_match('#[^\.]+#iu', $this -> new_file_name) == 0) {
			$this -> setNewFileNameToRandom();
		}
		$reserved_words = array('CON', 'PRN', 'AUX', 'CLOCK$', 'NUL', 'COM1', 'COM2', 'COM3', 'COM4', 'COM5', 'COM6', 'COM7', 'COM8', 'COM9', 'LPT1', 'LPT2', 'LPT3', 'LPT4', 'LPT5', 'LPT6', 'LPT7', 'LPT8', 'LPT9', 'LST', 'KEYBD$', 'SCREEN$', '$IDLE$', 'CONFIG$', '$Mft', '$MftMirr', '$LogFile', '$Volume', '$AttrDef', '$Bitmap', '$Boot', '$BadClus', '$Secure', '$Upcase', '$Extend', '$Quota', '$ObjId', '$Reparse', );
		foreach ($reserved_words as $reserved_word) {
			if (strtolower($reserved_word) == strtolower($this -> new_file_name)) {
				$this -> setNewFileNameToRandom();
			}
		}
		unset($reserved_word, $reserved_words);
		if ($this -> new_file_name == null) {
			$this -> setNewFileNameToRandom();
		}
	}

	/**
	 * 设置随机新文件名称
	 */
	protected function setNewFileNameToRandom() {
		$this -> new_file_name = uniqid() . '-' . str_replace('.', '', microtime(true));
	}

	/**
	 * 如果未设置MIME类型，则自动加载MIME配置
	 */
	protected function setupFileExtensionsMimeTypesForValidation() {
		if (!is_array($this -> file_extensions_mime_types) || $this -> file_extensions_mime_types == null) {
			$this -> file_extensions_mime_types = $this -> getMIME();
		}
	}

	/**
	 * 设置安全名称
	 */
	protected function setWebSafeFileName() {
		if ($this -> new_file_name == null) {
			$this -> setNewFileName();
		}
		$this -> new_file_name = preg_replace('#\s+#iu', ' ', $this -> new_file_name);
		$this -> new_file_name = str_replace(' ', '-', $this -> new_file_name);
		$this -> new_file_name = preg_replace('#[^\da-z\-_]#iu', '', $this -> new_file_name);
		$this -> new_file_name = preg_replace('#-{2,}#', '-', $this -> new_file_name);
	}

	/**
	 * 测试MIME类型
	 */
	public function testGetUploadedMimetype($input_file_name = null) {
		if ($input_file_name == null) {
			$input_file_name = $this -> input_file_name;
		}

		if (!isset($_FILES[$input_file_name]['name']) || (isset($_FILES[$input_file_name]['name']) && $_FILES[$input_file_name]['name'] == null) || !isset($_FILES[$input_file_name]['tmp_name']) || (isset($_FILES[$input_file_name]['tmp_name']) && $_FILES[$input_file_name]['tmp_name'] == null)) {
			return static::__(\Core::L("file_upload_null"));
		}

		if (!function_exists('finfo_open') || !function_exists('finfo_file')) {
			return static::__(\Core::L("file_function_error"));
		}

		$output = sprintf(static::__('File name: %s'), $_FILES[$input_file_name]['name']) . '<br>' . "\n";
		$file_name_exp = explode('.', $_FILES[$input_file_name]['name']);
		$file_extension = $file_name_exp[count($file_name_exp) - 1];
		unset($file_name_exp);
		$output .= sprintf(static::__('File extension: %s'), $file_extension) . '<br>' . "\n";

		$Finfo = new finfo();
		$file_mimetype = $Finfo -> file($_FILES[$input_file_name]['tmp_name'], FILEINFO_MIME_TYPE);
		$output .= sprintf(static::__('Mime type: %s'), $file_mimetype) . '<br>' . "\n";
		$output .= '<br>' . "\n";
		$output .= static::__('The array for use with extension-mime types validation.') . '<br>' . "\n";
		$output .= 'array(<br>' . "\n";
		$output .= '&nbsp; &nbsp; \'' . $file_extension . '\' =&gt; array(\'' . $file_mimetype . '\'),<br>' . "\n";
		$output .= ');' . "\n";
		unset($Finfo);

		if (is_writable($_FILES[$input_file_name]['tmp_name'])) {
			unlink($_FILES[$input_file_name]['tmp_name']);
		}

		unset($file_extension, $file_mimetype);
		return $output;
	}

	/**
	 * 开始上传
	 *
	 * @return boolean
	 */
	public function upload() {
		$this -> validateOptionsProperties();
		$this -> setupFileExtensionsMimeTypesForValidation();

		if (!is_dir($this -> move_uploaded_to)) {
			$this -> error_messages = array_merge($this -> error_messages, array(static::__(\Core::L("file_move_target_fail"))));
			return false;
		} elseif (is_dir($this -> move_uploaded_to) && !is_writable($this -> move_uploaded_to)) {
			$this -> error_messages = array_merge($this -> error_messages, array(static::__(\Core::L("file_move_target_permission_fail"))));
			return false;
		} else {
			$this -> move_uploaded_to = realpath($this -> move_uploaded_to);
		}

		if (isset($_FILES[$this -> input_file_name]['name']) && is_array($_FILES[$this -> input_file_name]['name'])) {
			foreach ($_FILES[$this->input_file_name]['name'] as $key => $value) {
				$this -> files[$this -> input_file_name]['input_file_key'] = $key;
				$this -> files[$this -> input_file_name]['name'] = $_FILES[$this -> input_file_name]['name'][$key];
				$this -> files[$this -> input_file_name]['type'] = (isset($_FILES[$this -> input_file_name]['type'][$key]) ? $_FILES[$this -> input_file_name]['type'][$key] : null);
				$this -> files[$this -> input_file_name]['tmp_name'] = (isset($_FILES[$this -> input_file_name]['tmp_name'][$key]) ? $_FILES[$this -> input_file_name]['tmp_name'][$key] : null);
				$this -> files[$this -> input_file_name]['error'] = (isset($_FILES[$this -> input_file_name]['error'][$key]) ? $_FILES[$this -> input_file_name]['error'][$key] : 4);
				$this -> files[$this -> input_file_name]['size'] = (isset($_FILES[$this -> input_file_name]['size'][$key]) ? $_FILES[$this -> input_file_name]['size'][$key] : 0);

				$result = $this -> uploadSingleFile();

				if ($result == false && $this -> stop_on_failed_upload_multiple === true) {
					unset($result);
					return false;
				}
			}
			unset($key, $value);
		} else {
			$this -> files[$this -> input_file_name] = $_FILES[$this -> input_file_name];
			$this -> files[$this -> input_file_name]['input_file_key'] = 0;

			$result = $this -> uploadSingleFile();
		}

		if (isset($result) && $result == false && $this -> stop_on_failed_upload_multiple === true) {
			unset($result);
			$this -> clearUploadedAtTemp();
			return false;
		} elseif (count($this -> error_messages) > 0 && $this -> stop_on_failed_upload_multiple === true) {
			unset($result);
			$this -> clearUploadedAtTemp();
			return false;
		}

		return $this -> moveUploadedFiles();
	}

	/**
	 * 上传单个文件
	 *
	 * @return boolean
	 */
	protected function uploadSingleFile() {
		if (is_array($this -> files[$this -> input_file_name]) && array_key_exists('error', $this -> files[$this -> input_file_name]) && $this -> files[$this -> input_file_name]['error'] != 0) {
			switch ($this->files[$this->input_file_name]['error']) {
				case 1 :
					$this -> error_messages = array_merge($this -> error_messages, array(sprintf(static::__(\Core::L("file_exceeds_size") . '(%s &gt; %s)'), $this -> files[$this -> input_file_name]['name'], ini_get('upload_max_filesize'))));
					return false;
				case 2 :
					$this -> error_messages = array_merge($this -> error_messages, array(static::__(\Core::L("file_exceeds_size"))));
					return false;
				case 3 :
					$this -> error_messages = array_merge($this -> error_messages, array(static::__(\Core::L("file_upload_partially"))));
					return false;
				case 4 :
					$this -> error_messages = array_merge($this -> error_messages, array(static::__(\Core::L("file_upload_didnt"))));
					return false;
				case 6 :
					$this -> error_messages = array_merge($this -> error_messages, array(static::__(\Core::L("missing_temporary_folder"))));
					return false;
				case 7 :
					$this -> error_messages = array_merge($this -> error_messages, array(static::__(\Core::L("write_file_fail"))));
					return false;
				case 8 :
					$this -> error_messages = array_merge($this -> error_messages, array(static::__(\Core::L("file_upload_didnt"))));
					return false;
			}
		}

		if (empty($this -> files[$this -> input_file_name]) || (is_array($this -> files[$this -> input_file_name]) && array_key_exists('name', $this -> files[$this -> input_file_name]) && $this -> files[$this -> input_file_name]['name'] == null) || (is_array($this -> files[$this -> input_file_name]) && array_key_exists('tmp_name', $this -> files[$this -> input_file_name]) && $this -> files[$this -> input_file_name]['tmp_name'] == null)) {
			$this -> error_messages = array_merge($this -> error_messages, array(static::__(\Core::L("file_upload_didnt"))));
			return false;
		}

		$result = $this -> validateExtensionAndMimeType();
		if ($result !== true) {
			return false;
		}
		unset($result);

		$result = $this -> validateFileSize();
		if ($result !== true) {
			return false;
		}
		unset($result);

		if ($this -> security_scan === true) {
			$result = $this -> securityScan();
			if ($result !== true) {
				return false;
			}
			unset($result);
		}

		$tmp_new_file_name = $this -> new_file_name;
		$this -> setNewFileName();

		if ($this -> web_safe_file_name === true) {
			$this -> setWebSafeFileName();
		}

		$file_name_explode = explode('.', $this -> files[$this -> input_file_name]['name']);
		$file_extension = null;
		if (is_array($file_name_explode)) {
			$file_extension = '.' . $file_name_explode[count($file_name_explode) - 1];
		}
		unset($file_name_explode);

		$this -> move_uploaded_queue = array_merge($this -> move_uploaded_queue, array($this -> files[$this -> input_file_name]['input_file_key'] => array('name' => $this -> files[$this -> input_file_name]['name'], 'tmp_name' => $this -> files[$this -> input_file_name]['tmp_name'], 'new_name' => $this -> new_file_name . $file_extension, )));

		$this -> new_file_name = $tmp_new_file_name;
		unset($file_extension, $tmp_new_file_name);

		return true;
	}

	protected function validateExtensionAndMimeType() {
		if ($this -> allowed_file_extensions == null && ($this -> file_extensions_mime_types == null || empty($this -> file_extensions_mime_types))) {
			return true;
		}

		$file_name_explode = explode('.', $this -> files[$this -> input_file_name]['name']);
		if (!is_array($file_name_explode)) {
			unset($file_name_explode);
			$this -> error_messages = array_merge($this -> error_messages, array(sprintf(static::__(\Core::L("unable_validate_extension") . ': %s.'), $this -> files[$this -> input_file_name]['name'])));
			return false;
		}
		$file_extension = $file_name_explode[count($file_name_explode) - 1];
		unset($file_name_explode);

		if (is_array($this -> allowed_file_extensions) && !in_array($file_extension, $this -> allowed_file_extensions)) {
			unset($file_extension);
			$this -> error_messages = array_merge($this -> error_messages, array(sprintf(static::__(\Core::L("file_upload_didnt") . '(%s)'), $this -> files[$this -> input_file_name]['name'])));
			return false;
		}

		if (is_array($this -> file_extensions_mime_types) && !empty($this -> file_extensions_mime_types)) {
			if (!array_key_exists($file_extension, $this -> file_extensions_mime_types)) {
				unset($file_extension);
				$this -> error_messages = array_merge($this -> error_messages, array(sprintf(static::__(\Core::L("unable_validate_extension") . '(%s) ' . \Core::L("unset_mime")), $this -> files[$this -> input_file_name]['name'])));
				return false;
			} else {
				$Finfo = new finfo();
				$file_mimetype = $Finfo -> file($this -> files[$this -> input_file_name]['tmp_name'], FILEINFO_MIME_TYPE);
				if (is_array($this -> file_extensions_mime_types[$file_extension]) && !in_array($file_mimetype, $this -> file_extensions_mime_types[$file_extension])) {
					unset($file_extension, $Finfo);
					$this -> error_messages = array_merge($this -> error_messages, array(sprintf(static::__(\Core::L("file_upload_didnt") . '(%s : %s)'), $this -> files[$this -> input_file_name]['name'], $file_mimetype)));
					unset($file_mimetype);
					return false;
				} elseif (!is_array($this -> file_extensions_mime_types[$file_extension])) {
					unset($file_extension, $file_mimetype, $Finfo);
					$this -> error_messages = array_merge($this -> error_messages, array(static::__(\Core::L("validate_mime_file_error"))));
					return false;
				}
				unset($file_mimetype, $Finfo);
			}
		}

		unset($file_extension);
		return true;
	}

	protected function validateFileSize() {
		if (!is_numeric($this -> max_file_size) && !is_int($this -> max_file_size)) {
			return true;
		}

		if (is_array($this -> files[$this -> input_file_name]) && array_key_exists('size', $this -> files[$this -> input_file_name]) && $this -> files[$this -> input_file_name]['size'] > $this -> max_file_size) {
			$this -> error_messages = array_merge($this -> error_messages, array(sprintf(static::__(\Core::L("file_exceeds_size") . '(%s &gt; %s bytes)'), $this -> files[$this -> input_file_name]['name'], $this -> max_file_size)));
			return false;
		}

		return true;
	}

	protected function validateOptionsProperties() {
		if (!is_array($this -> allowed_file_extensions) && $this -> allowed_file_extensions != null) {
			$this -> allowed_file_extensions = array($this -> allowed_file_extensions);
		}

		if (!is_array($this -> file_extensions_mime_types) && $this -> file_extensions_mime_types != null) {
			$this -> file_extensions_mime_types = null;
		}

		if (is_numeric($this -> max_file_size) && !is_int($this -> max_file_size)) {
			$this -> max_file_size = intval($this -> max_file_size);
		} elseif (!is_int($this -> max_file_size) && $this -> max_file_size != null) {
			$this -> max_file_size = null;
		}

		if ($this -> move_uploaded_to == null) {
			$this -> move_uploaded_to = '.';
		}

		if (!is_string($this -> new_file_name) && $this -> new_file_name != null) {
			$this -> new_file_name = null;
		}

		if (!is_bool($this -> overwrite)) {
			$this -> overwrite = false;
		}

		if (!is_bool($this -> web_safe_file_name)) {
			$this -> web_safe_file_name = true;
		}

		if (!is_bool($this -> security_scan)) {
			$this -> security_scan = false;
		}

		if (!is_bool($this -> stop_on_failed_upload_multiple)) {
			$this -> stop_on_failed_upload_multiple = true;
		}
	}

	protected function getMIME() {
		return array('7z' => array('application/x-7z-compressed'), 'aac' => array('audio/aac', 'audio/aacp', 'audio/x-aac'), 'avi' => array('video/x-msvideo'), 'bmp' => array('image/bmp'), 'css' => array('text/css'), 'csv' => array('application/csv', 'application/excel', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'application/x-csv', 'text/comma-separated-values', 'text/csv', 'text/x-comma-separated-values', 'text/x-csv'), 'doc' => array('application/msword'), 'docx' => array('application/vnd.openxmlformats-officedocument.wordprocessingml.document'), 'dvi' => array('application/x-dvi'), 'flv' => array('video/x-flv'), 'gif' => array('image/gif'), 'gz' => array('application/x-gzip'), 'h264' => array('video/h264'), 'h.264' => array('video/h264'), 'htm' => array('text/html'), 'html' => array('text/html'), 'jpg' => array('image/jpeg', 'image/pjpeg'), 'jpe' => array('image/jpeg', 'image/pjpeg'), 'jpeg' => array('image/jpeg', 'image/pjpeg'), 'js' => array('application/x-javascript', 'text/javascript'), 'json' => array('application/json', 'text/json'), 'log' => array('text/plain', 'text/x-log'), 'mid' => array('application/midi'), 'midi' => array('application/midi'), 'mov' => array('video/quicktime'), 'mp3' => array('audio/mp3', 'audio/mpeg', 'audio/mpeg3', 'audio/mpg'), 'mp4' => array('application/mp4', 'video/mp4'), 'mpe' => array('video/mpeg'), 'mpeg' => array('video/mpeg'), 'mpg' => array('video/mpeg'), 'pdf' => array('application/pdf', 'application/x-download'), 'png' => array('image/png', 'image/x-png'), 'ppt' => array('application/powerpoint', 'application/vnd.ms-powerpoint'), 'tar' => array('application/x-tar'), 'tiff' => array('image/tiff'), 'tif' => array('image/tiff'), 'tar' => array('application/x-tar'), 'tgz' => array('application/x-gzip-compressed', 'application/x-tar'), 'text' => array('text/plain'), 'ttf' => array('application/octet-stream', 'application/x-font-truetype', 'application/x-font-ttf'), 'txt' => array('text/plain'), 'pem' => array('text/plain'),'wav' => array('audio/wav', 'audio/wave', 'audio/x-wav'), 'webm' => array('audio/webm', 'video/webm'), 'xls' => array('application/excel', 'application/msexcel', 'application/vnd.ms-excel'), 'xlsx' => array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'), 'xml' => array('text/xml'), 'xsl' => array('text/xml'), 'zip' => array('application/x-zip', 'application/x-zip-compressed', 'application/zip'), );
	}

}
?>