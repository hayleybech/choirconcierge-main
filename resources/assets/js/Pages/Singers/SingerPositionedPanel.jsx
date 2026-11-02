import React, { useEffect } from 'react';
import { Portal } from '@headlessui/react';
import Popper from 'popper.js';

export const PositionedPanel = ({ component: PanelComponent, open, referenceRef, panelRef, placement, className, children }) => {
	useViewportAwarePopper(referenceRef, panelRef, open, placement);

	if (!open) {
		return null;
	}

	return (
		<Portal>
			<PanelComponent ref={panelRef} className={`fixed z-50 ${className}`}>
				{children}
			</PanelComponent>
		</Portal>
	);
};

export const useViewportAwarePopper = (referenceRef, panelRef, open, placement, positionKey) => {
	useEffect(() => {
		const reference = referenceRef.current;
		const panel = panelRef.current;

		if (!open || !reference || !panel) {
			return undefined;
		}

		const popper = new Popper(reference, panel, {
			placement,
			positionFixed: true,
			modifiers: {
				offset: { offset: '0, 8' },
				flip: { boundariesElement: 'viewport', padding: 8 },
				preventOverflow: { boundariesElement: 'viewport', padding: 8 },
			},
		});

		return () => popper.destroy();
	}, [open, panelRef, placement, positionKey, referenceRef]);
};