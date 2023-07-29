import React from 'react';
import classNames from "../../classNames";

const RiserStackHoldingArea = ({ voiceParts, singers, setSelectedSinger, selectedSinger, moveSelectedSingerToHoldingArea, showHeights }) => {

	function holdingAreaContainsSelectedSinger() {
		if(! selectedSinger){
			return false;
		}
		return singers.some(singer => singer.id === selectedSinger.id);
	}

	function toggleSelectedSinger(singer) {
		setSelectedSinger(singer !== selectedSinger ? singer : null);
	}

	return (
		<div
			className={classNames('h-full overflow-y-auto relative', selectedSinger && !holdingAreaContainsSelectedSinger() && 'ring-2 ring-purple-500')}
			aria-label="Holding Area"
		>
			{voiceParts.map((voicePart) =>
				<div key={voicePart.id} className="relative">
					<div className="z-10 sticky top-0 border-t border-b border-gray-200 bg-gray-50 px-6 py-1 text-sm font-medium text-gray-500">
						<h3 style={{ color: voicePart.colour }}>{voicePart.title}</h3>
					</div>
					<ul role="list" className="relative z-0 divide-y divide-gray-200">
						{singers.filter(singer => singer.enrolments.some(enrolment => enrolment.voice_part_id === voicePart.id)).map((singer) => (
							<li key={singer.id} >
								<div className={classNames(
									'relative px-6 py-5 flex items-center space-x-3',
									selectedSinger?.id === singer.id ? 'bg-purple-400 ring-2 ring-inset ring-purple-500' : 'bg-white hover:bg-purple-300'
								)}>
									<div className="shrink-0">
										<img className="h-8 w-8 rounded-lg" src={singer.user.avatar_url} alt={singer.user.name} />
									</div>
									<div className="flex items-center justify-between px-4">
										<button type="button" onClick={() => toggleSelectedSinger(singer)} className="focus:outline-none">
											{/* Extend touch target to entire panel */}
											<span className="absolute inset-0" aria-hidden="true" />
											<span className="text-sm font-medium truncate">{singer.user.name}</span>
										</button>
									</div>
									{showHeights && <div className="text-sm text-gray-500">({Math.round(singer.user.height)}cm)</div>}
								</div>
							</li>
						))}
					</ul>
				</div>
			)}
			{selectedSinger && !holdingAreaContainsSelectedSinger() &&
				<button type="button" onClick={moveSelectedSingerToHoldingArea} className="focus:outline-none">
					{/* Extend touch target to entire panel */}
					<span className="absolute inset-0 z-20 hover:bg-purple-400 opacity-50" aria-hidden="true"/>
				</button>
			}
		</div>
	);
}

export default RiserStackHoldingArea;