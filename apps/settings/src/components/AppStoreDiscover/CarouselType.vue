<template>
	<section>
		<h3 v-if="headline">
			{{ translatedHeadline }}
		</h3>
		<div class="app-discover-carousel__wrapper">
			<div class="app-discover-carousel__button-wrapper">
				<NcButton class="app-discover-carousel__button app-discover-carousel__button--previous"
					type="tertiary-no-background"
					:aria-label="t('settings', 'Previous')"
					:disabled="!hasPrevious"
					@click="currentIndex -= 1">
					<template #icon>
						<NcIconSvgWrapper :path="mdiChevronLeft" />
					</template>
				</NcButton>
			</div>

			<Transition :name="transitionName" mode="out-in">
				<PostType v-bind="shownElement" :key="shownElement.id ?? shownElement" inline />
			</Transition>

			<div class="app-discover-carousel__button-wrapper">
				<NcButton class="app-discover-carousel__button app-discover-carousel__button--next"
					type="tertiary-no-background"
					:aria-label="t('settings', 'Next')"
					:disabled="!hasNext"
					@click="currentIndex += 1">
					<template #icon>
						<NcIconSvgWrapper :path="mdiChevronRight" />
					</template>
				</NcButton>
			</div>
		</div>
		<div class="app-discover-carousel__tabs">
			<NcButton v-for="index of content.length"
				:key="index"
				:aria-label="t('settings', 'Slide {index}', { index })"
				type="tertiary-no-background"
				@click="currentIndex = index - 1">
				<template #icon>
					<NcIconSvgWrapper :path="currentIndex === (index - 1) ? mdiCircleSlice8 : mdiCircleOutline" />
				</template>
			</NcButton>
		</div>
	</section>
</template>

<script lang="ts">
import type { PropType } from 'vue'
import type { IAppDiscoverCarousel } from '../../constants/AppDiscoverTypes.ts'

import { mdiChevronLeft, mdiChevronRight, mdiCircleOutline, mdiCircleSlice8 } from '@mdi/js'
import { translate as t } from '@nextcloud/l10n'
import { computed, defineComponent, nextTick, ref, watch } from 'vue'
import { commonAppDiscoverProps } from './common.ts'
import { useLocalizedValue } from '../../composables/useGetLocalizedValue.ts'

import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'
import NcIconSvgWrapper from '@nextcloud/vue/dist/Components/NcIconSvgWrapper.js'
import PostType from './PostType.vue'

export default defineComponent({
	name: 'CarouselType',

	components: {
		NcButton,
		NcIconSvgWrapper,
		PostType,
	},

	props: {
		...commonAppDiscoverProps,

		/**
		 * The content of the carousel
		 */
		content: {
			type: Array as PropType<IAppDiscoverCarousel['content']>,
			required: true,
		},
	},

	setup(props) {
		const translatedHeadline = useLocalizedValue(computed(() => props.headline))
		// const translatedText = useLocalizedValue(computed(() => props.text))

		const currentIndex = ref(Math.min(1, props.content.length - 1))
		const shownElement = ref(props.content[currentIndex.value])
		const hasNext = computed(() => currentIndex.value < (props.content.length - 1))
		const hasPrevious = computed(() => currentIndex.value > 0)

		const transitionName = ref('slide-out')
		watch(() => currentIndex.value, (o, n) => {
			if (o < n) {
				transitionName.value = 'slide-out'
			} else {
				transitionName.value = 'slide-in'
			}

			// Wait next tick
			nextTick(() => {
				shownElement.value = props.content[currentIndex.value]
			})
		})

		return {
			t,

			hasNext,
			hasPrevious,
			currentIndex,
			shownElement,

			transitionName,

			translatedHeadline,
			// translatedText,

			mdiChevronLeft,
			mdiChevronRight,
			mdiCircleOutline,
			mdiCircleSlice8,
		}
	},
})
</script>

<style scoped lang="scss">
h3 {
	font-size: 24px;
	font-weight: 600;
	margin-block: 0 1em;
}

.app-discover-carousel {
	&__wrapper {
		display: flex;
	}

	&__button {
		color: var(--color-text-maxcontrast);
		position: absolute;
		top: calc(50% - 22px); // 50% minus half of button height

		&-wrapper {
			position: relative;
		}

		// See padding of discover section
		&--next {
			right: -54px;
		}
		&--previous {
			left: -54px;
		}
	}

	&__tabs {
		display: flex;
		flex-direction: row;
		justify-content: center;

		> * {
			color: var(--color-text-maxcontrast);
		}
	}
}
</style>

<style>
.slide-in-enter-active,
.slide-in-leave-active,
.slide-out-enter-active,
.slide-out-leave-active {
  transition: all .4s ease-out;
}

.slide-in-leave-to,
.slide-out-enter {
  opacity: 0;
  transform: translateX(50%);
}

.slide-in-enter,
.slide-out-leave-to {
  opacity: 0;
  transform: translateX(-50%);
}
</style>
