<?php
/**
 * Copyright (c) 2015 Petr Olišar (http://olisar.eu)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace Oli\GoogleAPI;

use Nette\Schema\Expect;
use Nette\Schema\Schema;

/**
 * Description of MapApiExtension
 *
 * @author Petr Olišar <petr.olisar@gmail.com>
 */
class MapApiExtension extends \Nette\DI\CompilerExtension{

	public function getConfigSchema():Schema{
		return Expect::structure([
			'key' => Expect::string(null)->nullable(),
			'width' => Expect::string('100%'),
			'height' => Expect::string('100%'),
			'zoom' => Expect::int(7),
			'coordinates' => Expect::array([]),
			'type' => Expect::string('ROADMAP'),
			'scrollable' => Expect::bool(true),
			'static' => Expect::bool(false),
			'markers' => Expect::structure([
				'bound' => Expect::bool(false),
				'markerClusterer' => Expect::bool(false),
				'iconDefaultPath' => Expect::string(null)->nullable(),
				'icon' => Expect::string(null)->nullable(),
				'addMarkers' => Expect::array([]),
			]),
		]);
	}


	public function loadConfiguration(){
		$config = (array)$this->getConfig();
		$config['markers'] = (array)$config['markers'];
		$builder = $this->getContainerBuilder();

		$builder->addFactoryDefinition($this->prefix('mapAPI'))
			->setImplement('Oli\GoogleAPI\IMapAPI')
			->getResultDefinition()->setFactory('Oli\GoogleAPI\MapAPI')
			->addSetup('setup', [$config])
			->addSetup('setKey', [$config['key']])
			->addSetup('setCoordinates', [$config['coordinates']])
			->addSetup('setType', [$config['type']])
			->addSetup('isStaticMap', [$config['static']])
			->addSetup('isScrollable', [$config['scrollable']])
			->addSetup('setZoom', [$config['zoom']]);

		$builder->addFactoryDefinition($this->prefix('markers'))
			->setImplement('Oli\GoogleAPI\IMarkers')
			->getResultDefinition()->setFactory('Oli\GoogleAPI\Markers')
			->addSetup('setDefaultIconPath', [$config['markers']['iconDefaultPath']])
			->addSetup('fitBounds', [$config['markers']['bound']])
			->addSetup('isMarkerClusterer', [$config['markers']['markerClusterer']])
			->addSetup('addMarkers', [$config['markers']['addMarkers']]);
	}

}
