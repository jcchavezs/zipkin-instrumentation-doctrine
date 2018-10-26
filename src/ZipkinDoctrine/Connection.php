<?php

namespace ZipkinDoctrine;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\DBALException;
use Exception;
use Zipkin\Tags;
use Zipkin\Tracer;

final class Connection extends DBALConnection
{
    /**
     * @var Tracer;
     */
    private $tracer;

    /**
     * @var array
     */
    private $options;

    /**
     * @var Tracer $tracer
     * @var array $options
     */
    public function setTracer(Tracer $tracer, $options = [])
    {
        $this->tracer = $tracer;
        $options = is_array($options) ? $options : [];
        $this->options = [
            'affected_rows' => array_key_exists('affected_rows', $options)
            ? $options['affected_rows'] : false,
        ];
    }

    /**
     * {@inhertidoc}
     */
    public function executeQuery($query, array $params = array(), $types = array(), QueryCacheProfile $qcp = null)
    {
        if ($this->tracer === null) {
            return parent::executeQuery($query, $params, $types, $qcp);
        }

        $span = $this->tracer->nextSpan();
        $span->setName('sql/query');
        $span->tag(Tags\SQL_QUERY, $query);

        try {
            $span->start();
            $stmt = parent::executeQuery($query, $params, $types, $qcp);
            if ($this->shouldTraceAffectedRows()) {
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
        $span->setName('sql/update');
        $span->tag(Tags\SQL_QUERY, $query);

        try {
            $span->start();
            $affectedRows = parent::executeUpdate($query, $params, $types);
            if ($this->shouldTraceAffectedRows()) {
                $span->tag('sql.affected_rows', $affectedRows);
            }
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
        $span->setName('sql/exec');
        $span->tag(Tags\SQL_QUERY, $statement);

        try {
            $span->start();
            $affectedRows = parent::exec($statement);
            if ($this->shouldTraceAffectedRows()) {
                $span->tag('sql.affected_rows', $affectedRows);
            }
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
    public function prepare($statement)
    {
        if ($this->tracer == null) {
            return parent::prepare($statement);
        }

        try {
            $stmt = new Statement($statement, $this, $this->tracer);
        } catch (Exception $ex) {
            throw DBALException::driverExceptionDuringQuery($this->_driver, $ex, $statement);
        }

        $stmt->setFetchMode($this->defaultFetchMode);

        return $stmt;
    }

    /**
     * @return bool
     */
    private function shouldTraceAffectedRows()
    {
        return $this->options['affected_rows'];
    }
}
