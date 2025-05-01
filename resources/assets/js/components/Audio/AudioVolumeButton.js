import React, { ChangeEvent, useCallback } from "react"
import { useAudioPlayer } from "react-use-audio-player"
import Button from "../inputs/Button";
import { Popover } from '@headlessui/react'
import resolveConfig from 'tailwindcss/resolveConfig'
import tailwindConfig from '../../../../../tailwind.config'
import Icon from "../Icon";

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
            <Popover.Button as={Button} variant="clear" size="xs">
                <Icon icon="volume" />
            </Popover.Button>
            <Popover.Panel className="absolute bottom-full right-0 bg-white p-3 rounded border border-gray-200 shadow-lg">
                <div className="flex items-center space-x-2">
                    <Icon icon="volume-down" className="text-gray-700" />
                    <input
                         type="range"
                         min={0}
                         max={100}
                         onChange={handleChange}
                         defaultValue={100}
                         style={{ accentColor: fullConfig.theme.colors.purple[500] ?? '#ff0000' }}
                    />
                    <Icon icon="volume-up" className="text-gray-700" />
                </div>
            </Popover.Panel>
        </Popover>
    )
}