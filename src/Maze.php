<?php

namespace JanisGruzis;

use JanisGruzis\Exception\MazeException;

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
     * @var int
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
     * @param int $width
     * @param int $height
     * @param int $seed
     */
    public function __construct($width = 8, $height = 8, $seed = 0)
    {
        $this->setWidth($width);
        $this->setHeight($height);
        $this->setSeed($seed);
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
     * @param int $seed
     *
     * @return $this
     * @throws MazeException
     */
    public function setSeed($seed)
    {
        if (!is_int($seed)) {
            throw new MazeException('Seed has to be an integer.');
        }

        $this->seed = $seed;

        return $this;
    }

    /**
     * Kruskal's algorithm
     *
     * @return array
     */
    public function generate()
    {
        $result = [];

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
                $k = $i * $this->width + $j;
                $result[$i][$j] = 15;
                $this->parent[$k] = $k;
                $this->rank[$k] = 1;

                foreach ($sides as $key => $side) {
                    $_i = $i + $side[0];
                    $_j = $j + $side[1];
                    $_k = $_i * $this->width + $_j;

                    if ($_i >= 0 && $_i < $this->width && $_j >= 0 && $_j < $this->height) {
                        $this->edges[] = [$k, $_k, $key];
                    }
                }
            }
        }

        $randomOrder = array_map(create_function('$val', 'return mt_rand();'), range(1, count($this->edges)));
        array_multisort($randomOrder, $this->edges);

        foreach ($this->edges as $edge) {
            if ($this->union($edge[0], $edge[1])) {
                $i = $edge[0] / $this->width;
                $j = $edge[0] % $this->width;
                $result[$i][$j] &= ~(1 << $edge[2]);
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
