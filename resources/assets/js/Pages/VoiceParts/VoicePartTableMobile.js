import React from 'react';
import Swatch from "../../components/Swatch";
import TableMobile, { TableMobileHeader, TableMobileItem } from '../../components/TableMobile';
import useRoute from "../../hooks/useRoute";
import { plural } from '../../util';

const VoicePartTableMobile = ({ voiceParts }) => {
    const { route } = useRoute();

	const bulkEdit = {
		isActiveMobile: false,
		noun: 'Part',
		selectedIds: [],
		totalItems: voiceParts.length,
	};

    return (
		<div>
			<TableMobileHeader bulkEdit={bulkEdit} />
			<TableMobile>
				{voiceParts.map(voicePart => (
					<TableMobileItem key={voicePart.id} url={route('voice-parts.edit', { voice_part: voicePart.id })}>
						<div className="shrink-0">
							<Swatch colour={voicePart.colour} />
						</div>
						<div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
							<div>
								<div className="flex items-center justify-between">
									<p className="flex items-center min-w-0 mr-1.5">
										<span className="text-sm font-medium text-purple-600 truncate">
											{voicePart.title}
										</span>
									</p>
									<p className="flex items-center min-w-0 mr-1.5">
										<span className="text-sm font-medium text-gray-500">
											{voicePart.singers_count}{' '}
											{plural('singer', voicePart.singers_count)}
										</span>
									</p>
								</div>
							</div>
						</div>
					</TableMobileItem>
				))}
			</TableMobile>
		</div>
	);
}

export default VoicePartTableMobile;