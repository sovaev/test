<?php
$fib = new Fibonachi();
$req = $fib->getValidate($_GET);
echo $req;
class Fibonachi {
	public $redisConnect = [
        'host' => 'redis',
        'port' => 6379
    ];
	
	/**
	 * Определяем редис
	 */
	
	public function __construct()
    {
        $this->redis = new Redis;
        $this->redis->connect($this->redisConnect['host'], $this->redisConnect['port']);
    }

    /** @var Redis */
    protected $redis;

	/**
	 * @param array $get
	 * @return string
	 */
	public function getValidate($get)
	{
		$from = $get['from'] ?? $get['to'] ?? null;
        $to = $get['to'] ?? $get['from'] ?? null;

		if (!$this->intCheck($from) || !$this->intCheck($to)) {
			return $this->error('1'); // Введены не целочисленные значения
		}

		if ($to < $from) {
            return $this->error('2'); // Промежуток задан не корректно
        }

        $manyNum = $this->getManyValue($from, $to);
        return $this->returnData($this->getReturnData($to, $from, $manyNum));
	}

    /**
     * @param $val
     * @return bool
     */
	
	public function intCheck($val)
	{
        return preg_match('/^\+?\d+$/', $val) && $val > 0;
	}

    /**
     * @param string $message
     * @return string
     */

	public function error($message)
    {
        return $this->returnData(['error' => $message]);
    }

    /**
     * @param array $data
     * @return string
     */

	public function returnData($data)
    {
        return json_encode($data);
    }

    /**
     * @param int $to
     * @param int $from
     * @param array $data
     * @return array
     */
	
	public function getReturnData($to, $from, $data)
	{
		$reBuildData = [];
		for ($i = $from; $i <= $to; $i++) {
			$reBuildData[$i] = (int)$data[$i];
		}

		return $reBuildData;
	}

	/**
	 * @param int $from
	 * @param int $to
	 * @return array
	 */
	
	public function getManyValue($from, $to)
    {
		$preData = [];
		for ($i = $from; $i <= $to; $i++) {
			if ($i < 3) {
				$preData[$i] = 1;
				$this->setData($i, $preData[$i]);
			} else {
				$check = $this->getCheckOneValue($i);
				if ($check === false) {
					break;
				} else {
					$preData[$i] = $check;
				}	
			}
			if ($i == $to) {
				return $preData;
			}
		}
		$lengtData = count($preData)+$from;
		if ($from < $lengtData AND $lengtData <= $to) {
			for ($i = $from+1; $i <= $to; $i++) {
				$preData[$i] = bcadd($preData[$i-1], $preData[$i-2]);
				$this->setData($i, $preData[$i]);
			}
			return $preData;
		} elseif ($lengtData == $from) {
			$nums = $this->buildFibonachi($from);
			$preData = [
				$from-1 => $nums[$from-1],
				$from 	=> $nums[$from]
			];
			for ($i = $from+1; $i <= $to; $i++) {
				$preData[$i] = bcadd($preData[$i-1], $preData[$i-2]);
				$this->setData($i, $preData[$i]);
			}
			return $preData;
		}
	}
	
	
	/**
	 * @param int $number
	 * @return array
	 */
	
	public function buildFibonachi($number)
	{
		$preData = [];
		for ($i = 1; $i <= $number; $i++) {
            $preData[$i] = $this->getCheckOneValue($i);
		    if (!$preData[$i]) {
		        $preData[$i] = $i < 3 ? 1 : bcadd($preData[$i-1], $preData[$i-2]);
                $this->setData($i, $preData[$i]);
            }
		}
		# возвращаем требуемый элемент и 2 предыдущих
		return [$number =>$preData[$number], $number-1 => $preData[$number-1]];
	}
	
	/**
	 * @param int $key
	 * @param int $value
	 */
	
	public function setData($key, $value)
	{
		$this->redis->set($key, $value);
	}
	
	/**
	 * @param array $data
	 * @return array|bool
	 */
	
	public function getCheckPreValue($data)
	{
		$preData = [];
		foreach ($data as $val){
			$request = $this->redis->get($val);
			if ($request === false) {
				return false;
			} else {
				$preData[$val] = $request;
			}
		}
		$newNum = bcadd($preData[$data[0]], $preData[$data[1]]);
		$preData[$data[1]+1] = $newNum;	
		return $preData;
	}
	
	/**
	 * @param int $val
	 * @return int|bool
	 */
	
	public function getCheckOneValue($val)
	{
		return $this->redis->get($val);
	}
}
