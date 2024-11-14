<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Widget content --}}
        <!-- Heading -->
        <div class="flex flex-col space-y-1.5 pb-6">
            <h2 class="text-lg font-semibold tracking-tight">DocumentChat</h2>
            <p class="text-sm text-[#4775d1] leading-3">Powered by AskYourPDF</p>
        </div>

        <!-- Chat Container -->
        <div class="pr-4 h-[474px] overflow-y-auto" style="min-width: 100%; display: table;">
            @foreach ($messages as $msg)
                @switch($msg['sender'])
                    @case('bot')
                        <!-- Chat Message IA -->
                        <div class="flex flex-1 gap-3 my-4 text-sm text-gray-600">
                            <span class="relative flex w-8 h-8 overflow-hidden rounded-full shrink-0">
                                <div class="p-1 bg-gray-100 border rounded-full">
                                    <svg stroke="none" fill="black" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"
                                        height="20" width="20" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z">
                                        </path>
                                    </svg>
                                </div>
                            </span>
                            <p class="leading-relaxed">
                                <span class="block font-bold text-gray-700">IA </span> {{$msg['message']}}
                            </p>
                        </div>
                    @break

                    @case('user')
                        <!-- User Chat Message -->
                        <div class="flex flex-1 gap-3 my-4 text-sm text-gray-600">
                            <span class="relative flex w-8 h-8 overflow-hidden rounded-full shrink-0">
                                <div class="p-1 bg-gray-100 border rounded-full">
                                    <svg stroke="none" fill="black" stroke-width="0" viewBox="0 0 16 16" height="20" width="20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z">
                                        </path>
                                    </svg>
                                </div>
                            </span>
                            <p class="leading-relaxed">
                                <span class="block font-bold text-gray-700">Vous </span>{{$msg['message']}}
                            </p>
                        </div>
                    @break
                @endswitch
            @endforeach
        </div>

        <!-- Input box -->
        <div class="flex items-center pt-0">
            <form class="flex items-center justify-center w-full space-x-2" wire:submit.prevent="ask">
                <textarea
                    class="flex h-10 w-full rounded-md border border-[#e5e7eb] px-3 py-2 text-sm placeholder-[#6b7280] focus:outline-none focus:ring-2 focus:ring-[#9ca3af] disabled:cursor-not-allowed disabled:opacity-50 focus-visible:ring-offset-2 fi-input border-none text-gray-950 transition duration-75 placeholder:text-gray-400 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 bg-white/0 ps-3 pe-3"
                    placeholder="Type your message" id="question" wire:model="question"></textarea>

                <button style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);"
                    class="inline-flex rounded-md text-sm h-10 px-4 py-2 fi-btn relative items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 fi-color-custom fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50 fi-ac-action fi-ac-btn-action"
                    type="submit">
                    <span class="fi-btn-label">
                        Send
                    </span>
                </button>
            </form>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>