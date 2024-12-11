<?php

namespace Autoframe\Core\Event;

//TODO de mergiuit cu clasa de log pt ury ca am acolo timings
use Autoframe\Core\Afr\Afr;
use Autoframe\Core\Env\Exception\AfrEnvException;
use Autoframe\Core\Event\Exception\AfrEventException;
use Autoframe\Core\String\Obj\AfrClosureToStr;
use Throwable;
use Closure;

class AfrEvent
{
//	const AFR_EVENT = 'afr.event'; //applies on any event
//	const AFR_EXCEPTION = 'afr.exception';
	const AFR_BOOTSTRAP = 'afr.bootstrap';
	const AFR_RUN = 'afr.run';
	const DB_ACTION_START = 'db.action.start0';
	const DB_QUERY_START = 'db.query.start1';
	const DB_QUERY_END = 'db.query.complete2';
	const DB_FETCH_START = 'db.fetch.start3';
	const DB_FETCH_DONE = 'db.fetch.done4';
	const DB_ACTION_DONE = 'db.action.done5';

	const MODULE_READ_START = 'mod.read.start';
	const MODULE_READ_END = 'mod.read.end';
	const MODULE_LOAD = 'mod.load';

	const HTTP_HEADER_SET = 'http.header.set';
	const HTTP_COOKIE_SET = 'http.cookie.set';
	const HTTP_STATUS_SET = 'http.status.set';
	const HTTP_REDIRECT = 'http.redirect';

	const SESSION_START = 'session.start';
	const SESSION_READ = 'session.read';
	const SESSION_WRITE = 'session.write';

	const ROUTER_BEFORE_ROUTING_0 = 'router.before.routing0';
	const ROUTER_ON_MIDDLEWARE_1 = 'router.on.middleware1';
	const ROUTER_ON_CODE_2 = 'router.on.code2';
	const ROUTER_ON_AFTER_3 = 'router.on.after3';
	const ROUTER_BEFORE_VIEW_4 = 'router.before.view4';
	const ROUTER_VIEW_RENDER_START_5 = 'router.view.render.start5';
	const ROUTER_VIEW_RENDER_DONE_6 = 'router.view.render.done6';
	const ROUTER_VIEW_OUTPUT_START_7 = 'router.view.output.start7';
	const ROUTER_VIEW_OUTPUT_DONE_8 = 'router.view.output.done8';


	const CACHE_READ_START = 'cache.read.start';
	const CACHE_READ_END = 'cache.read.end';
	const CACHE_HIT = 'cache.hit';
	const CACHE_MISS = 'cache.miss';

	const X_EVT = 'e';
	const X_ARGS = 'a';
	const X_TRACE = 't';
	const X_TIMING = 'm';
	const X_WILDCARDS = 'w';


	const  WILDCARD_STARTS_WITH = 'afr.*'; //applies on any event starting with afr.*
	const  WILDCARD_ENDS_WITH = '*afr'; //applies on any event ending with *afr
	const  WILDCARD_CONTAINS = '*afr*'; //applies on any event containing the substring
	const  WILDCARD_STAR = '*'; //applies on any event

	public static bool $bSlimTrace = true;
	public static int $iTraceDepth = 5;

	public static ?bool $bHrTime = false;

	protected static array $aTriggeredEventsStats = [];
	public static array $aTriggeredEventsResults = [];
	protected static array $aOnEventClosures = [];
	protected static array $aWildcardEvents = [];


	/**
	 * @param string $sEvent
	 * @param array|null $aArgs
	 * @param int|null $iTraceDepth
	 * @return array
	 * @throws AfrEventException
	 */
	public static function dispatchEvent(
		string $sEvent,
		array  $aArgs = null,
		int    $iTraceDepth = null
	): array
	{
		$sEvent = $sEvent ?: static::autoEventName(3);
		$iTraceDepth = self::getTraceDepth($iTraceDepth, 'EV_TRACE_DEPTH_' . $sEvent);

		$aTrace = $iTraceDepth ?
			self::slimDebugTrace(
				debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $iTraceDepth),
				self::$bSlimTrace
			) : null;

		$aData = [
			self::X_EVT => $sEvent,
			self::X_TIMING => static::hrMicroTime(),
		];
		if ($aArgs) {
			$aData[self::X_ARGS] = $aArgs;
		}
		if ($aTrace) {
			$aData[self::X_TRACE] = $aTrace;
		}
		$aResults = self::callEventClosure($sEvent, $aData, []);

		//wildcards
		foreach (self::$aWildcardEvents ?? [] as $sWildcardEvent => $aHow) {
			$bTrigger = ($sWildcardEvent === self::WILDCARD_STAR);
			if (!$bTrigger && !empty($aHow[self::WILDCARD_STARTS_WITH]) && $aHow[self::WILDCARD_STARTS_WITH][0] ===
				substr($sEvent, 0, $aHow[self::WILDCARD_STARTS_WITH][1])) {
				$bTrigger = true;
			}
			if (!$bTrigger && !empty($aHow[self::WILDCARD_ENDS_WITH]) && $aHow[self::WILDCARD_ENDS_WITH][0] ===
				substr($sEvent, -$aHow[self::WILDCARD_ENDS_WITH][1], $aHow[self::WILDCARD_ENDS_WITH][1])) {
				$bTrigger = true;
			}
			if (!$bTrigger && !empty($aHow[self::WILDCARD_CONTAINS])) {
				foreach ($aHow[self::WILDCARD_CONTAINS] as $sContains) {
					if (strpos($sEvent, $sContains) !== false) {
						$bTrigger = true;
						break;
					}
				}
			}
			if (!$bTrigger) {
				continue;
			}
			$aData[self::X_WILDCARDS] ??= [];
			$aData[self::X_WILDCARDS][] = [
				self::X_EVT => $sWildcardEvent,
				self::X_TIMING => static::hrMicroTime(),
			];
			$aResults = self::callEventClosure($sWildcardEvent, $aData, $aResults);
		}

		self::$aTriggeredEventsStats[] = $aData;
		return self::$aTriggeredEventsResults[] = $aResults;
	}


	/**
	 * @param string $sEvent
	 * @param array $aData
	 * @param array $aResults
	 * @return array
	 * @throws AfrEventException
	 */
	protected static function callEventClosure(string $sEvent, array $aData, array $aResults): array
	{
		$oScopeInstance = null;
		foreach (self::$aOnEventClosures[$sEvent] ?? [] as $i => $aClosureAndScopeBound) {
			/** @var Closure $closure */
			[$closure, $bInnerBoundScope, $newScope] = $aClosureAndScopeBound;
			if ($bInnerBoundScope) {
				if ($oScopeInstance === null) { //detect once on each trace
					$oScopeInstance = false;
					foreach (debug_backtrace(3) as $aTrace) {
						if (!empty($aTrace['object']) && is_object($aTrace['object'])) {
							$oScopeInstance = $aTrace['object'];
							break;
						}
					}
				}
				if ($oScopeInstance) {
					if ($oScopeInstance instanceof Throwable) {
						$oScopeInstance = false;
						continue; //prevent infinite loop inside error scoping
					}
					$newBoundClosure = $closure->bindTo(
						$oScopeInstance,
						$newScope === true ? $oScopeInstance : $newScope
					);
					if ($newBoundClosure instanceof Closure) {
						$closure = $newBoundClosure;
					} else {
						$bFatal = false;
						if (Afr::app()) {
							try {
								$bFatal = Afr::app()->env()->getEnv('AFR_EVENT_BIND_EXCEPTION_IS_FATAL_ERROR', true);
							} catch (Throwable $e) {
								$bFatal = true;
							}
						}
						if ($bFatal) {
							unset($aClosureAndScopeBound[0]);
							throw new AfrEventException(
								'Unable to bind #index.' . $i . ' from event(' . $sEvent . ', ' . $aData[self::X_EVT] .
								') to scope(' . get_class($oScopeInstance) . ') for Closure(scope,' .
								implode(',', $aClosureAndScopeBound) . '): ' . AfrClosureToStr::dump($closure)
							);
						}
					}
				}
			}
			// FINALLY, call
			$aResults[$sEvent] = $closure($aData); //call closure
		}

		return $aResults;
	}


	protected static function autoEventName(int $iStackLevel = 3): string
	{
		$last = $iStackLevel - 1;
		$prev = $iStackLevel - 2;
		$aEvents = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, $iStackLevel);
		if (!empty($aEvents[$last]['class']) && !empty($aEvents[$last]['function'])) {
			return array_slice(explode('\\', $aEvents[$last]['class']), -1, 1)[0] .
				'.' . $aEvents[$last]['function'];
		}
		return basename($aEvents[$prev]['file']) . '.L' . $aEvents[$prev]['line'];
	}


	/**
	 * @param string $sEvent Event name: db.query.start or wildcard: db.* | *.start | db.*.start | *.*.*.rt
	 * @param Closure $closure What to do, and receive ($aData)
	 * @param bool $bPrependQueue the closure should be placed at the stack beginning or appended at the end?
	 * @param bool $bInnerBoundScope true = $newThis on Closure::bindTo(?object $newThis, object|string|null $newScope = "static")
	 * @param object|string|null $newScope true = $newThis|"static"|null
	 * @return void
	 */
	public static function addEventClosure(
		string  $sEvent,
		Closure $closure,
		bool    $bInnerBoundScope = false,
		        $newScope = true,
		bool    $bPrependQueue = false
	): void
	{
		self::$aOnEventClosures[$sEvent] ??= [];
		if ($bPrependQueue) {
			self::$aOnEventClosures[$sEvent] = array_merge(
				[[$closure, $bInnerBoundScope, $newScope]],
				self::$aOnEventClosures[$sEvent]
			);
		} else {
			self::$aOnEventClosures[$sEvent][] = [$closure, $bInnerBoundScope, $newScope];
		}

		self::addWildcardEvent($sEvent);

	}

	public static function getTriggeredEventsLog(): array
	{
		return self::$aTriggeredEventsStats;
	}

	public static function getEventClosures(): array
	{
		return self::$aOnEventClosures;
	}

	public static function getWildcardConfig(): array
	{
		return self::$aWildcardEvents;

	}

	/**
	 * @param int|null $iTraceDepth
	 * @param string $sEnvTraceLimitKey
	 * @return int
	 */
	protected static function getTraceDepth(?int $iTraceDepth, string $sEnvTraceLimitKey): int
	{
		if (is_null($iTraceDepth)) {
			$iTraceDepth = self::$iTraceDepth;
			if (Afr::app()) {
				try {
					$iTraceDepth = max(
						(int)Afr::app()->env()->getEnv($sEnvTraceLimitKey, $iTraceDepth),
						$iTraceDepth
					);
				} catch (Throwable $e) {
				}
			} elseif (!empty($_ENV[$sEnvTraceLimitKey])) {
				$iTraceDepth = max((int)$_ENV[$sEnvTraceLimitKey], $iTraceDepth);
			}
		}
		return (int)$iTraceDepth;
	}

	/**
	 * @param array $aDebugBacktrace
	 * @param bool $bSlim
	 * @return array
	 */
	protected static function slimDebugTrace(array $aDebugBacktrace, bool $bSlim): array
	{
		$aTrace = [];
		foreach ($aDebugBacktrace as $aInfo) {
			$file = strtr($aInfo['file'] ?? '[PHP_Kernel]', '\\', '/');
			if ($bSlim) {
				$file = implode(
					'/',
					array_slice(explode('/', $file), -2, 2)
				);
			}
			$sClass = '';
			if (!empty($aInfo['object']) && is_object($aInfo['object'])) {
				$sClass = get_class($aInfo['object']);
			} elseif (!empty($aInfo['class'])) {
				$sClass = $aInfo['class'];
			}
			if (!empty($sClass)) {
				$sClass = ($bSlim ? basename($sClass) : $sClass) . ($aInfo['type'] ?? ' ');
			}

			$aTrace[] = $file . ':' . ($aInfo['line'] ?? 0) . "\t" . trim($sClass) . ($aInfo['function'] ?? '') . '()';
		}
		return $aTrace;
	}

	/**
	 * @return float
	 */
	protected static function hrMicroTime(): float
	{
		static::$bHrTime ??= function_exists('hrtime');
		return static::$bHrTime ? (hrtime(true) / 1e+6) : microtime(true);
	}

	/**
	 * @param string $sEvent
	 * @return void
	 */
	protected static function addWildcardEvent(string $sEvent): void
	{
		if (empty(self::$aWildcardEvents[$sEvent]) && substr_count($sEvent, self::WILDCARD_STAR) > 0) {
			$aMap = [];
			if ($sEvent === self::WILDCARD_STAR) { //*
				self::$aWildcardEvents[$sEvent] = $aMap[self::WILDCARD_STAR] = true; //match any event
			} else {
				$aSections = explode(self::WILDCARD_STAR, $sEvent);
				$iSections = count($aSections);

				foreach ($aSections as $i => $sSection) {
					if ($i === 0) {
						$aMap[self::WILDCARD_STARTS_WITH] = [$sSection, strlen($sSection)];
					} elseif ($i === $iSections - 1) {
						$aMap[self::WILDCARD_ENDS_WITH] = [$sSection, strlen($sSection)];
					} else {
						$aMap[self::WILDCARD_CONTAINS] = array_merge(
							$aMap[self::WILDCARD_CONTAINS] ?? [],
							[$sSection]
						);
					}
				}
			}
			self::$aWildcardEvents[$sEvent] = $aMap;
		}
	}
}