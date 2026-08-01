<?php

namespace App\Services;

class FpGrowthService
{
    private float $minSupportCount = 0.0;
    private float $minConfidence = 0.0;

    /**
     * Jalankan analisis FP-Growth.
     * 
     * @param array $transactions Format biasa [['A', 'B']] ATAU [['items' => ['A', 'B'], 'meta' => [...]]]
     * @param float $minSupport Persentase (0-100)
     * @param float $minConfidence Persentase (0-100)
     */
    public function run(array $transactions, float $minSupport, float $minConfidence): array
    {
        $totalTransactions = count($transactions);
        if ($totalTransactions === 0) {
            return [];
        }

        $this->minSupportCount = ($minSupport / 100) * $totalTransactions;
        $this->minConfidence = $minConfidence / 100;

        // Extract items and metadata
        $cleanBaskets = [];
        $metaBaskets = [];

        foreach ($transactions as $idx => $tx) {
            if (is_array($tx) && isset($tx['items']) && is_array($tx['items'])) {
                $cleanBaskets[$idx] = $tx['items'];
                $metaBaskets[$idx] = $tx['meta'] ?? [];
            } else {
                $cleanBaskets[$idx] = (array)$tx;
                $metaBaskets[$idx] = [];
            }
        }

        // 1. Hitung frekuensi item & filter berdasarkan minSupport
        $itemFrequencies = [];
        foreach ($cleanBaskets as $basket) {
            foreach (array_unique($basket) as $item) {
                $itemFrequencies[$item] = ($itemFrequencies[$item] ?? 0) + 1;
            }
        }

        // Filter frequent items
        $frequentItems = array_filter($itemFrequencies, function ($count) {
            return $count >= $this->minSupportCount;
        });

        // Urutkan item berdasarkan frekuensi descending
        arsort($frequentItems);
        $sortedItemNames = array_keys($frequentItems);

        // 2. Bersihkan dan urutkan transaksi
        $cleanedTransactions = [];
        $cleanedMetas = [];
        foreach ($cleanBaskets as $idx => $basket) {
            $cleaned = [];
            foreach ($sortedItemNames as $item) {
                if (in_array($item, $basket)) {
                    $cleaned[] = $item;
                }
            }
            if (!empty($cleaned)) {
                $cleanedTransactions[] = $cleaned;
                $cleanedMetas[] = $metaBaskets[$idx];
            }
        }

        // 3. Pair Counts & Sample Order Collectors
        $rules = [];
        $pairCounts = [];
        $pairSamples = [];
        $individualCounts = $frequentItems;

        foreach ($cleanedTransactions as $idx => $transaction) {
            $meta = $cleanedMetas[$idx];
            $len = count($transaction);
            for ($i = 0; $i < $len; $i++) {
                for ($j = $i + 1; $j < $len; $j++) {
                    $pair = $transaction[$i] . '|||' . $transaction[$j];
                    $pairCounts[$pair] = ($pairCounts[$pair] ?? 0) + 1;

                    if (!empty($meta) && isset($meta['no_pesanan'])) {
                        if (!isset($pairSamples[$pair])) {
                            $pairSamples[$pair] = [];
                        }
                        if (count($pairSamples[$pair]) < 10) {
                            $alreadyIn = false;
                            foreach ($pairSamples[$pair] as $existing) {
                                if ($existing['no_pesanan'] === $meta['no_pesanan']) {
                                    $alreadyIn = true;
                                    break;
                                }
                            }
                            if (!$alreadyIn) {
                                $pairSamples[$pair][] = $meta;
                            }
                        }
                    }
                }
            }
        }

        // 4. Generate Association Rules dengan Metrik Lift Ratio
        foreach ($pairCounts as $pair => $count) {
            if ($count >= $this->minSupportCount) {
                list($itemA, $itemB) = explode('|||', $pair);
                $samples = $pairSamples[$pair] ?? [];

                // Rule A -> B
                $confidenceAB = $count / $individualCounts[$itemA];
                if ($confidenceAB >= $this->minConfidence) {
                    $supportB = $individualCounts[$itemB] / $totalTransactions;
                    $liftAB = round($confidenceAB / $supportB, 2);

                    $rules[] = [
                        'ante' => $itemA,
                        'cons' => $itemB,
                        'support' => round(($count / $totalTransactions) * 100, 2),
                        'confidence' => round($confidenceAB * 100, 2),
                        'lift_ratio' => $liftAB,
                        'count_both' => $count,
                        'count_ante' => $individualCounts[$itemA],
                        'count_cons' => $individualCounts[$itemB],
                        'total_transactions' => $totalTransactions,
                        'sample_orders' => $samples,
                    ];
                }

                // Rule B -> A
                $confidenceBA = $count / $individualCounts[$itemB];
                if ($confidenceBA >= $this->minConfidence) {
                    $supportA = $individualCounts[$itemA] / $totalTransactions;
                    $liftBA = round($confidenceBA / $supportA, 2);

                    $rules[] = [
                        'ante' => $itemB,
                        'cons' => $itemA,
                        'support' => round(($count / $totalTransactions) * 100, 2),
                        'confidence' => round($confidenceBA * 100, 2),
                        'lift_ratio' => $liftBA,
                        'count_both' => $count,
                        'count_ante' => $individualCounts[$itemB],
                        'count_cons' => $individualCounts[$itemA],
                        'total_transactions' => $totalTransactions,
                        'sample_orders' => $samples,
                    ];
                }
            }
        }

        // Urutkan berdasarkan confidence tertinggi
        usort($rules, function($a, $b) {
            if ($b['confidence'] == $a['confidence']) {
                return $b['lift_ratio'] <=> $a['lift_ratio'];
            }
            return $b['confidence'] <=> $a['confidence'];
        });

        return $rules;
    }
}
