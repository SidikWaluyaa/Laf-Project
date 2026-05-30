<?php

namespace App\Services;

class FpGrowthService
{
    private $minSupportCount;
    private $minConfidence;

    /**
     * Jalankan analisis FP-Growth.
     * 
     * @param array $transactions Contoh: [['A', 'B'], ['A', 'C'], ...]
     * @param float $minSupport Persentase (0-100)
     * @param float $minConfidence Persentase (0-100)
     */
    public function run($transactions, $minSupport, $minConfidence)
    {
        $totalTransactions = count($transactions);
        $this->minSupportCount = ($minSupport / 100) * $totalTransactions;
        $this->minConfidence = $minConfidence / 100;

        // 1. Hitung frekuensi item & filter berdasarkan minSupport
        $itemFrequencies = [];
        foreach ($transactions as $transaction) {
            foreach (array_unique($transaction) as $item) {
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
        foreach ($transactions as $transaction) {
            $cleaned = [];
            foreach ($sortedItemNames as $item) {
                if (in_array($item, $transaction)) {
                    $cleaned[] = $item;
                }
            }
            if (!empty($cleaned)) {
                $cleanedTransactions[] = $cleaned;
            }
        }

        // 3. Bangun FP-Tree (Simple Version for Recommendation Rules)
        // Untuk skripsi, kita akan fokus mencari pola berpasangan (paling umum untuk promo)
        $rules = [];
        $pairCounts = [];
        $individualCounts = $frequentItems;

        foreach ($cleanedTransactions as $transaction) {
            $len = count($transaction);
            for ($i = 0; $i < $len; $i++) {
                for ($j = $i + 1; $j < $len; $j++) {
                    $pair = $transaction[$i] . ',' . $transaction[$j];
                    $pairCounts[$pair] = ($pairCounts[$pair] ?? 0) + 1;
                }
            }
        }

        // 4. Generate Association Rules
        foreach ($pairCounts as $pair => $count) {
            if ($count >= $this->minSupportCount) {
                list($itemA, $itemB) = explode(',', $pair);
                
                // Rule A -> B
                $confidenceAB = $count / $individualCounts[$itemA];
                if ($confidenceAB >= $this->minConfidence) {
                    $rules[] = [
                        'ante' => $itemA,
                        'cons' => $itemB,
                        'support' => round(($count / $totalTransactions) * 100, 2),
                        'confidence' => round($confidenceAB * 100, 2),
                        'count_both' => $count,
                        'count_ante' => $individualCounts[$itemA],
                        'count_cons' => $individualCounts[$itemB],
                        'total_transactions' => $totalTransactions
                    ];
                }

                // Rule B -> A
                $confidenceBA = $count / $individualCounts[$itemB];
                if ($confidenceBA >= $this->minConfidence) {
                    $rules[] = [
                        'ante' => $itemB,
                        'cons' => $itemA,
                        'support' => round(($count / $totalTransactions) * 100, 2),
                        'confidence' => round($confidenceBA * 100, 2),
                        'count_both' => $count,
                        'count_ante' => $individualCounts[$itemB],
                        'count_cons' => $individualCounts[$itemA],
                        'total_transactions' => $totalTransactions
                    ];
                }
            }
        }

        // Urutkan berdasarkan confidence tertinggi
        usort($rules, function($a, $b) {
            return $b['confidence'] <=> $a['confidence'];
        });

        return $rules;
    }
}
