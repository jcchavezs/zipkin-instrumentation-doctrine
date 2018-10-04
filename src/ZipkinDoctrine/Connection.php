<?php

namespace ZipkinDoctrine;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Statement;
use Zipkin\Tags;
use Zipkin\Tracer;

final class Connection extends DBALConnection
{
    /**
     * @var Tracer;
     */
    private $tracer;

    /**
     * @var Tracer $tracer
     */
    public function setTracer(Tracer $tracer)
    {
        $this->tracer = $tracer;
    }

    /**
     * {@inhertidoc}
     */
    public function executeQuery($query, array $params = array(), $types = array(), ?QueryCacheProfile $qcp = null)
    {
        if ($this->tracer === null) {
            return parent::executeQuery($query, $params, $types, $qcp);
        }

        $span = $this->tracer->nextSpan();
        $span->setName('query');
        $span->tag(Tags\SQL_QUERY, $query);

        try {
            $span->start();
            $stmt = parent::executeQuery($query, $params, $types, $qcp);
            if (method_exists($stmt, 'rowCount')) {
                $span->tag('sql.affected_rows', $stmt->rowCount());
            }
            return $stmt;
        } catch (DBALException $e) {
            $span->tag(Tags\ERROR, $e->getMessage());
            throw $e;
        } finally {
            $span->finish();
        }
    }

    /**
     * {@inhertidoc}
     */
    public function executeUpdate($query, array $params = array(), array $types = array())
    {
        if ($this->tracer === null) {
            return parent::executeUpdate($query, $params, $types);
        }

        $span = $this->tracer->nextSpan();
        $span->setName('query');
        $span->tag(Tags\SQL_QUERY, $query);

        try {
            $span->start();
            $affectedRows = parent::executeUpdate($query, $params, $types);
            $span->tag('sql.affected_rows', $affectedRows);
            return $affectedRows;
        } catch (DBALException $e) {
            $span->tag(Tags\ERROR, $e->getMessage());
            throw $e;
        } finally {
            $span->finish();
        }
    }

    /**
     * {@inhertidoc}
     */
    public function exec($statement)
    {
        if ($this->tracer === null) {
            return parent::exec($statement);
        }

        $span = $this->tracer->nextSpan();
        $span->setName('query');
        $span->tag(Tags\SQL_QUERY, $statement);

        try {
            $span->start();
            $affectedRows = parent::exec($statement);
            $span->tag('sql.affected_rows', $affectedRows);
            return $affectedRows;
        } catch (DBALException $e) {
            $span->tag(Tags\ERROR, $e->getMessage());
            throw $e;
        } finally {
            $span->finish();
        }
    }
}
