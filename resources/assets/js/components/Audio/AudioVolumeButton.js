import React, { ChangeEvent, useCallback } from "react"
import { useAudioPlayer } from "react-use-audio-player"
import Button from "../inputs/Button";
import { Popover } from '@headlessui/react'
import resolveConfig from 'tailwindcss/resolveConfig'
import tailwindConfig from '../../../../../tailwind.config'

export const AudioVolumeButton = () => {
    const { volume } = useAudioPlayer();
    const fullConfig = resolveConfig(tailwindConfig);

    const handleChange = useCallback(
        (slider) => {
            const volValue = parseFloat(
                (Number(slider.target.value) / 100).toFixed(2)
            )
            return volume(volValue);
        },
        [volume]
    )

    return (
        <Popover className="relative">
            <Popover.Button as={Button} variant="clear" size="sm">
                <i className="fas fa-fw fa-volume" />
            </Popover.Button>
            <Popover.Panel className="absolute bottom-full right-0 bg-white p-3 rounded border border-gray-200 shadow-lg">
                <div className="flex items-center space-x-2">
                    <i className="fa fa-volume-down text-gray-800" />
                    <input
                         type="range"
                         min={0}
                         max={100}
                         onChange={handleChange}
                         defaultValue={100}
                         style={{ accentColor: fullConfig.theme.colors.purple[500] ?? '#ff0000' }}
                    />
                     <i className="fa fa-volume-up text-gray-800" />
                </div>
            </Popover.Panel>
        </Popover>
    )
}