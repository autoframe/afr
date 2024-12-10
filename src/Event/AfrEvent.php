<?php

namespace Autoframe\Core\Event;

//TODO de mergiuit cu clasa de log pt ury ca am acolo timings
use Autoframe\Core\Afr\Afr;

class AfrEvent
{
//	const AFR_EVENT = 'afr.event'; //applies on any event
	const AFR_BOOTSTRAP = 'afr.bootstrap';
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


	const  WILDCARD_STARTS_WITH = 'afr.*'; //applies on any event starting with afr.*
	const  WILDCARD_ENDS_WITH = '*afr'; //applies on any event ending with *afr
	const  WILDCARD_CONTAINS = '*afr*'; //applies on any event containing the substring
	const  WILDCARD_STAR = '*'; //applies on any event

	public static bool $bSlimTrace = true;
	public static int $iTraceDepth = 4;

	public static ?bool $bHrTime = null;

	protected static array $aTriggeredEventsLog = [];
	protected static array $aOnEventClosures = [];
	protected static array $aBoundToInnerScopeClosures = [];
	protected static array $aWildcardEvents = [];

	//todo de facut aici cu stack trace / timings / log? single responsability
	public static function addEvent(
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
			self::X_ARGS => $aArgs,
			self::X_TRACE => $aTrace,
		];
		$aReturn = self::callEventClosure($sEvent, $aData, []);

		//inner scope
		foreach (self::$aBoundToInnerScopeClosures ?? [] as $sWildcardEvent => $aHow) {
			if ($sWildcardEvent !== self::WILDCARD_STAR) {
				if (
					!empty($aHow[self::WILDCARD_STARTS_WITH]) &&
					substr($sEvent, 0, $aHow[self::WILDCARD_STARTS_WITH][1]) !== $aHow[self::WILDCARD_STARTS_WITH][0]
				) {
					continue;
				}
			}
			$aData[self::X_TIMING] = static::hrMicroTime();
			$aReturn = self::callEventClosure($sWildcardEvent, $aData, $aReturn);
		}

		return $aReturn;
	}


	protected static function callEventClosure(string $sEvent, array $aData, array $aReturn = []): array
	{
		self::$aTriggeredEventsLog[] = $aData;
		$iTriggeredEventsLogIndex = count(self::$aTriggeredEventsLog[]) - 1;

		//targeted event
		//TODO wildcard events!!!
		/** @var \Closure $closure */
		if ($sEvent !== self::WILDCARD_STAR) {
			foreach (self::$aOnEventClosures[$sEvent] ?? [] as $closure) {
				$aReturn[] = $closure($aData);
			}
		}

		//generic event
		foreach (self::$aOnEventClosures[self::WILDCARD_STAR] ?? [] as $closure) {
			$aReturn[] = $closure($aData);
		}

		if (!empty(self::$aBoundToInnerScopeClosures[$sEvent]) || !empty(self::$aBoundToInnerScopeClosures[self::WILDCARD_STAR])) {
			$oScopeInstance = null;
			foreach (debug_backtrace(3) as $aTrace) {
				if (!empty($aTrace['object'])) {
					$oScopeInstance = $aTrace['object'];
					break;
				}
			}
			//TODO test!!!!
			if ($oScopeInstance) {
				//any event to inner scope
				if ($sEvent !== self::WILDCARD_STAR) {
					foreach (self::$aBoundToInnerScopeClosures[$sEvent] ?? [] as $closure) {
						$aReturn[] = $closure->bindTo($oScopeInstance, $oScopeInstance)($aData);
					}
				}

				//generic event to inner scope
				foreach (self::$aBoundToInnerScopeClosures[self::WILDCARD_STAR] ?? [] as $closure) {
					$aReturn[] = $closure->bindTo($oScopeInstance, $oScopeInstance)($aData);
				}
			}

		}


		//something has changed inside the aData, because of closures using reference
		//TODO test affirmation
		if (self::$aTriggeredEventsLog[$iTriggeredEventsLogIndex] !== $aData) {
			self::$aTriggeredEventsLog[$iTriggeredEventsLogIndex] = $aData;
		}


		return $aReturn;
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
	 * @param \Closure $closure What to do, and receive ($aData)
	 * @param bool $bInnerBoundScope bindTo($this,$this) inside the instance that triggered the event
	 * @return void
	 */
	public static function addOnEventClosure(string $sEvent, \Closure $closure, bool $bInnerBoundScope): void
	{
		if ($bInnerBoundScope) {
			self::$aBoundToInnerScopeClosures[$sEvent] ??= [];
			self::$aBoundToInnerScopeClosures[$sEvent][] = $closure;
		} else {
			self::$aOnEventClosures[$sEvent] ??= [];
			self::$aOnEventClosures[$sEvent][] = $closure;
		}

		if (empty(self::$aWildcardEvents[$sEvent]) && substr_count($sEvent, self::WILDCARD_STAR) > 0) {
			$aMap = [];
			if ($sEvent === self::WILDCARD_STAR) { //*
				self::$aWildcardEvents[$sEvent] = $aMap[self::WILDCARD_STAR] = true; //match any event
			} else {
				$aSections = explode(self::WILDCARD_STAR, $sEvent);
				$iSections = count($aSections);
				if (($iLen = strlen($aSections[0])) > 0) {
					$aMap[self::WILDCARD_STARTS_WITH] = [$aSections[0], $iLen];
				}
				if (($iLen = strlen($aSections[$iSections - 1])) > 0) {
					$aMap[self::WILDCARD_ENDS_WITH] = [$aSections[$iSections - 1], $iLen];
				}
				if ($iSections > 2) {
					foreach ($aSections as $i => $sSection) {
						if ($i === 0 || $i === $iSections - 1 || strlen($sSection) === 0) {
							continue;
						}
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

	public static function getTriggeredEventsLog(): array
	{
		return self::$aTriggeredEventsLog;
	}

	public static function getEventClosures(bool $bGetInnerScopeOnly = false): array
	{
		return $bGetInnerScopeOnly ? self::$aBoundToInnerScopeClosures : self::$aOnEventClosures;
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
				} catch (\Throwable $e) {
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

			$aTrace[] = $file . ':' . ($aInfo['line'] ?? 0) . "\t" . $sClass . ($aInfo['function'] ?? '') . '()';
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
}