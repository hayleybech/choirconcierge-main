import React from 'react';
import ButtonV2 from '../inputs/ButtonV2';
import { Popover } from '@headlessui/react';
import Icon from '../Icon';
import { VolumeControl } from './VolumeControl';
import { TempoControl } from './TempoControl';
import { PitchControl } from './PitchControl';
import { PanControl } from './PanControl';

export const AudioToolsButton = () => (
	<Popover className="relative">
		<Popover.Button as={ButtonV2} intent="secondary" weight="clear" size="sm" className="tabular-nums w-12">
			<Icon icon="sliders-v" size="text-lg" />
		</Popover.Button>
		<Popover.Panel className="absolute bottom-full mb-2 space-y-4 right-0 bg-white rounded border border-gray-200 shadow-lg py-4 min-w-max w-[400px] max-w-[calc(100vw_-_16px)] px-3">
			<div>
				<VolumeControl />
			</div>

			<div>
				<PanControl />
			</div>

			<div>
				<TempoControl />
			</div>

			<div>
				<PitchControl />
			</div>
		</Popover.Panel>
	</Popover>
);
