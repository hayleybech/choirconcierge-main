import React from "react";
import { act, render, screen, waitFor } from "@testing-library/react";
import SwitchChoirMenu from "./SwitchChoirMenu";
import "@testing-library/jest-dom";
import userEvent from "@testing-library/user-event";

global.ResizeObserver = class {
  observe() {}
  unobserve() {}
  disconnect() {}
};

jest.mock('ziggy-js', () => ({
  __esModule: true,
  ...jest.requireActual('ziggy-js'),
  default: () => '', // aka route()
}));

describe('SwitchChoirMenu', () => {
  it('lists all of the groups for the current user', async () => {
    render(<SwitchChoirMenu
      choirs={[
        {
          id: 1,
          logo_url: 'choir1.png',
          name: 'Choir Number 1',
        },
        {
          id: 2,
          logo_url: 'choir2.png',
          name: 'Choir Number 2',
        }
      ]}
      tenant={{
        id: 2,
        logo_url: 'choir2.png',
        name: 'Choir Number 2',
      }}
    />);

    const button = screen.getByRole('button', { name: 'Choir Number 2' });
    expect(button).toBeInTheDocument();

	await act(async () => {
		await userEvent.click(button);
	})
    await waitFor(() => expect(screen.getByRole('menu')).toBeInTheDocument());

    expect(screen.getByRole('menuitem', {name: 'Choir Number 1'})).toBeInTheDocument();
    expect(screen.getByRole('menuitem', {name: 'Choir Number 2'})).toBeInTheDocument();
  });

  it('disables opening the menu when there is one group or less', () => {
    render(<SwitchChoirMenu
      choirs={[
        {
          id: 2,
          logo_url: 'choir2.png',
          name: 'Choir Number 2',
        }
      ]}
      tenant={{
        id: 2,
        logo_url: 'choir2.png',
        name: 'Choir Number 2',
      }}
    />);

    expect(screen.getByRole('button', { name: 'Choir Number 2' })).toBeDisabled();
  });

  it('allows choosing a group when none is selected', () => {
    render(<SwitchChoirMenu
      choirs={[
        {
          id: 2,
          logo_url: 'choir2.png',
          name: 'Choir Number 2',
        }
      ]}
      tenant={null}
    />);

    const button = screen.getByRole('button', { name: 'Switch Choir' });
    expect(button).toBeInTheDocument();
    expect(button).toBeEnabled();
  });

  it('opens the choir list in a modal on mobile', async () => {
    render(<SwitchChoirMenu
      mobile
      choirs={[{id: 1, name: 'Choir Number 1'}, {id: 2, name: 'Choir Number 2'}]}
      tenant={{id: 1, name: 'Choir Number 1'}}
    />);

    await userEvent.click(screen.getByRole('button', {name: 'Choir Number 1'}));

    expect(screen.getByRole('dialog')).toBeInTheDocument();
    expect(screen.getByRole('link', {name: 'Choir Number 2'})).toBeInTheDocument();
  });
});
