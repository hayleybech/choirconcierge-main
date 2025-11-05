import Button from '../inputs/Button';
import Icon from '../Icon';
import PitchButton from '../PitchButton';
import React, {Fragment, useCallback, useState} from 'react';
import PitchScale from '../PitchScale';
import {Menu, Transition} from '@headlessui/react';
import buttonStyles from '../inputs/buttonStyles';
import {DateTime} from "luxon";
import {useKonami} from "react-konami-code";

const PdfToolbar = ({ isFullscreen, closeFullscreen, openFullscreen, zoomIn, zoomOut, pitch, instrument, setInstrument }) => {
	const [showPitchBar, setShowPitchBar] = useState(false);

	return (
		<>
			<div className="flex flex-wrap p-1.5 gap-x-2.5 border-b border-gray-300">
				<div className="flex gap-x-1">
					<Button onClick={() => zoomIn()} size="sm" className="h-7">
						<Icon icon="search-plus" />
					</Button>
					<Button onClick={() => zoomOut()} size="sm" className="h-7">
						<Icon icon="search-minus" />
					</Button>
				</div>

				<div className="flex gap-x-1">
					<PitchButton instrument={instrument} note={pitch} size="sm" className="h-7" />
					<Button onClick={() => setShowPitchBar(value => !value)} size="sm" className="h-7">
						<Icon icon="piano-keyboard" />
					</Button>

					<InstrumentMenu instrument={instrument} setInstrument={setInstrument} />
				</div>

				<Button
					variant="secondary"
					onClick={isFullscreen ? closeFullscreen : openFullscreen}
					size="sm"
					className="h-7 ml-auto"
				>
					<Icon icon={isFullscreen ? 'times' : 'expand'} />
				</Button>
			</div>

			{showPitchBar && <PitchScale instrument={instrument} />}
		</>
	);
};

export default PdfToolbar;

const InstrumentMenu = (props) => {
	const [showSecrets, setShowSecrets] = useState(false);
	const unlockAll = useCallback(() => {
		props.instrument.instrument.triggerAttackRelease('C4', 0.5);
		props.instrument.instrument.triggerAttackRelease('E4', 0.5);
		props.instrument.instrument.triggerAttackRelease('G4', 0.5);
		props.instrument.instrument.triggerAttackRelease('C5', 0.5);

		setShowSecrets(true);
	}, [props.instrument])
	useKonami(unlockAll);

	return (
		<SimpleMenu label={
			<>
				{props.instrument.name === 'sine' && (
					<><Icon icon="wave-sine" mr /> Synth</>
				)}
				{props.instrument.name === 'piano' && (
					<><Icon icon="piano" mr /> Piano</>
				)}
				{props.instrument.name === 'pipe' && (
					<><Icon icon="whistle" mr /> Pipe</>
				)}
				{props.instrument.name === 'cat' && (
					<><Icon icon="cat" mr /> Cat</>
				)}
				{props.instrument.name === 'wilhelm' && (
					<><Icon icon="jack-o-lantern" mr /> Wilhelm</>
				)}
				{props.instrument.name === 'sleigh' && (
					<><Icon icon="sleigh" mr /> Sleigh</>
				)}
			</>
		}>
			<SimpleMenuItem onClick={() => props.setInstrument('sine')}><Icon icon="wave-sine" mr /> Synth</SimpleMenuItem>
			<SimpleMenuItem onClick={() => props.setInstrument('piano')}><Icon icon="piano" mr /> Piano</SimpleMenuItem>
			<SimpleMenuItem onClick={() => props.setInstrument('pipe')}><Icon icon="whistle" mr /> Pipe</SimpleMenuItem>
			<SimpleMenuItem onClick={() => props.setInstrument('cat')}><Icon icon="cat" mr /> Cat</SimpleMenuItem>
			{(showSecrets || DateTime.now().month === 10) && (
				<SimpleMenuItem onClick={() => props.setInstrument('wilhelm')}><Icon icon="jack-o-lantern" mr /> Wilhelm</SimpleMenuItem>
			)}
			{(showSecrets || DateTime.now().month === 12) && (
				<SimpleMenuItem onClick={() => props.setInstrument('sleigh')}><Icon icon="sleigh" mr /> Sleigh</SimpleMenuItem>
			)}
		</SimpleMenu>
	)
}

const SimpleMenu = props => (
	<Menu as="div" className="relative inline-block">
		<Menu.Button className={buttonStyles('secondary', 'sm', false, 'h-7 text-sm')}>
			{props.label}
			{/*<ChevronDownIcon aria-hidden="true" className="-mr-1 size-5 text-gray-400" />*/}
			<Icon icon="chevron-down" size="sm" />
		</Menu.Button>

		<Transition
			as={Fragment}
			enter="transition ease-out duration-100"
			enterFrom="transform opacity-0 scale-95"
			enterTo="transform opacity-100 scale-100"
			leave="transition ease-in duration-75"
			leaveFrom="transform opacity-100 scale-100"
			leaveTo="transform opacity-0 scale-95"
		>
			<Menu.Items className="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg outline-1 outline-black/5"
			>
				<div className="py-1">{props.children}</div>
			</Menu.Items>
		</Transition>
	</Menu>
);

const SimpleMenuItem = props => (
	<Menu.Item>
		<button
			onClick={props.onClick}
			className="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:bg-gray-100 hover:text-gray-900 focus:text-gray-900 hover:outline-hidden focus:outline-hidden text-left"
		>
			{props.children}
		</button>
	</Menu.Item>
);
