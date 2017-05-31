<?php

namespace JanisGruzis;

/**
 * Class Maze
 *
 * @package JanisGruzis
 */
class Maze
{
    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var null|string
     */
    private $seed;

    /**
     * @var array
     */
    private $parent = [];

    /**
     * @var array
     */
    private $rank = [];

    /**
     * @var array
     */
    private $edges = [];

    /**
     * Maze constructor.
     *
     * @param int         $width
     * @param int         $height
     * @param string|null $seed
     */
    public function __construct($width = 8, $height = 8, $seed = null)
    {
        $this->width = $width;
        $this->height = $height;
        $this->seed = $seed ?? uniqid();
    }

    /**
     * @param int $width
     *
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @param int $height
     *
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @param string $seed
     *
     * @return $this
     */
    public function setSeed($seed)
    {
        $this->seed = $seed;

        return $this;
    }

    /**
     * @return array
     */
    public function generate()
    {
        $result = [];
        $k = 0;

        $this->cleanup();
        mt_srand($this->seed);
        $sides = [
            [0, -1],
            [1, 0],
            [0, 1],
            [-1, 0],
        ];

        for ($i = 0; $i < $this->width; ++$i) {
            $result[$i] = [];
            for ($j = 0; $j < $this->height; ++$j) {
                $result[$i][$j] = 15;
                $this->parent[$k] = $k;
                $this->rank[$k] = 1;

                foreach ($sides as $key => $side) {
                    $ip = $i + $side[0];
                    $jp = $j + $side[1];
                    if ($ip >= 0 && $ip < $this->width && $jp >= 0 && $jp < $this->height) {
                        $this->edges[] = [$k, $ip + $this->width * $jp, $key];
                    }
                }

                $k++;
            }
        }

        $order = array_map(create_function('$val', 'return mt_rand();'), range(1, count($this->edges)));
        array_multisort($order, $this->edges);

        foreach ($this->edges as $edge) {
            if ($this->union($edge[0], $edge[1])) {
                $result[$edge[0]] &= ~(1 << $edge[2]);
            }
        }

        $this->cleanup();

        return $result;
    }

    /**
     * @param int $a
     * @param int $b
     *
     * @return bool
     */
    private function union($a, $b)
    {
        $a = $this->find($a);
        $b = $this->find($b);

        if ($a == $b) {
            return false;
        }

        if ($this->rank[$a] > $this->rank[$b]) {
            $this->parent[$a] = $b;
            $this->rank[$a] += $this->rank[$b];
        } else {
            $this->parent[$b] = $a;
            $this->rank[$b] += $this->rank[$a];
        }

        return true;
    }

    /**
     * @param int $i
     *
     * @return int
     */
    private function find($i)
    {
        return $this->parent[$i] == $i
            ? $i
            : $this->find($this->parent[$i]);
    }

    /**
     * Cleanup data for algorithm.
     */
    private function cleanup()
    {
        $this->parent = [];
        $this->rank = [];
        $this->edges = [];
    }
}
