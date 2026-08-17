import { Popover } from '@headlessui/react';
import Button from '../inputs/Button';
import Icon from '../Icon';
import React from 'react';
import Label from '../inputs/Label';
import TextInput from '../inputs/TextInput';

export const MetronomeButton = () => {
	return (
		<Popover className="relative flex">
			<Button variant="secondary" size="sm" className="rounded-none rounded-l">
				<Icon icon="play" />
				<Icon icon="hourglass-half" />
			</Button>
			<Popover.Button as={Button} variant="secondary" size="sm" className="-ml-px rounded-none px-0.5 rounded-r">
				<Icon icon="chevron-down" />
			</Popover.Button>
			<Popover.Panel className="absolute top-full mt-2 z-10 space-y-4 right-0 bg-white rounded border border-gray-200 shadow-lg py-4 min-w-max w-[400px] max-w-[calc(100vw_-_16px)] px-3">
				<div>
					<Label>Tempo</Label>
					<div className="flex gap-1 items-center grow">
						<TextInput className="grow" />
						<Label>BPM</Label>
					</div>
				</div>
				<div>
					<Label>Beats per Measure</Label>
					<TextInput value={4} />
				</div>
			</Popover.Panel>
		</Popover>
	);
}