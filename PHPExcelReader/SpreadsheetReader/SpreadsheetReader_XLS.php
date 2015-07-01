<?php

class SpreadsheetReader_XLS implements Iterator, Countable {
	private $handle = false;
	private $index = 0;
	private $rowCount = null;
	private $currentSheet = 0;
	private $currentRow = null;
	
	public  $error = false;
	
	public function __construct($filePath) {
		self::classLoad();
		$this->handle = new Spreadsheet_Excel_Reader($filePath);
		if ($this->handle->error) {
			$this->error = true;
			return false;
		}
	}
	
	public function __destruct() {
		unset($this->handle);
	}
	
	/**
	 * Retrieves an array with information about sheets in the current file
	 *
	 * @return array List of sheets (key is sheet index, value is name)
	 */
	public function Sheets() {
		$this->sheetInfo = $this->handle->getWorksheetInfo();
		$this->rowCount = $this->sheetInfo['totalRows'];
		
		return $this->sheetInfo;
	}
	
	/**
	 * Changes the current sheet in the file to another
	 * @param $index int
	 * @return bool
	 */
	public function ChangeSheet($index)	{
		return $this->handle->ChangeSheet($index);
	}
	
	/**
	 * Rewind the Iterator to the first element.
	 */
	public function rewind() {
		$this->index = 0;
	}
	
	/**
	 * Return the current element.
	 * @return mixed
	 */
	public function current() {
		if ($this->index == 0 && is_null($this->currentRow)) {
			$this->next();
			$this->index = 0;
		}

		return $this->currentRow;
	}
	
	/**
	 * Move forward to next element.
	 */
	public function next() {
		$this->currentRow = array();
		
		$this->index++;
		$this->currentRow = $this->handle->getCell();
		
		return $this->currentRow;
	}
	
	/**
	 * Return the identifying key of the current element.
	 * @return mixed
	 */
	public function key() {
		return $this->index;
	}
	
	/**
	 * Check if there is a current element after calls to rewind() or next().
	 * @return boolean
	 */
	public function valid()	{
		if ($this->error) {
			return false;
		}
		
		return ($this->index < $this->count());
	}
	
	/**
	 * return the count of the contained items
	 */
	public function count() {
		if ($this->error) {
			return 0;
		}
		
		if(is_null($this->rowCount)){
			$this->Sheets();
		}
		
		return $this->rowCount;
	}
	
	private static function classLoad()	{
		if ( ! class_exists('Spreadsheet_Excel_Reader', false)) {
			require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'SpreadsheetReader.php';
		}
	}
}