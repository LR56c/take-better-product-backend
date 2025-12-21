<?php

namespace Src\Products\Infrastructure;

use Illuminate\Support\Facades\DB;
use Pgvector\Laravel\Vector;
use Src\Products\Domain\ProductEmbedding;
use Src\Products\Domain\ProductEmbeddingRepository;

class EloquentProductEmbeddingRepository implements ProductEmbeddingRepository
{
    public function save(ProductEmbedding $embedding): void
    {
        $embedding->save();
    }

    public function searchSimilar(array $vector, int $limit): array
    {
        $vectorString = (new Vector($vector))->__toString();

        // We must inject the vector string directly into the query because
        // PDO binding would wrap it in quotes, breaking the pgvector syntax.
        // This is safe because the vector is generated internally.
        $query = "SELECT product_id, 1 - (vector <-> '{$vectorString}') AS similarity
                  FROM product_embeddings
                  ORDER BY vector <-> '{$vectorString}'
                  LIMIT {$limit}";

        $results = DB::select($query);

        return array_map(fn($row) => [
            'product_id' => $row->product_id,
            'similarity' => (float) $row->similarity,
        ], $results);
    }
}
