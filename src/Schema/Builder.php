<?php

namespace onezerotrash\Firebird\Schema;

use Illuminate\Database\Schema\Builder as SchemaBuilder;

class Builder extends SchemaBuilder
{
    // # ALTERADO POR CODEX: método público imposto pelo Laravel; preserva listagem sem API de schema moderna.
    public function getColumnListing($table)
    {
        return $this->connection->getPostProcessor()->processColumnListing(
            $this->connection->selectFromWriteConnection(
                $this->grammar->compileColumnListing($this->connection->getTablePrefix().$table)
            )
        );
    }
}
