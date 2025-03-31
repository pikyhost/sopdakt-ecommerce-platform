<div>
    <!-- Your Incoming Search Trigger -->
    <div class="header-icon header-search header-search-popup header-search-category text-right">
        <a href="#" class="search-toggle" role="button"><i class="icon-magnifier"></i></a>
    </div>

    <!-- Search Modal -->
    <div class="search-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: #fff; border-radius: 12px; width: 90%; max-width: 600px; padding: 25px; box-shadow: 0 5px 30px rgba(0,0,0,0.1);">
            <!-- Modal Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: #333; margin: 0;">Search</h3>
                <button class="close-modal" style="background: none; border: none; color: #666; font-size: 24px; cursor: pointer;">&times;</button>
            </div>

            <!-- Search Input and Results -->
            <div class="search-container" style="position: relative;">
                <div class="search-wrapper" style="display: flex; align-items: center; background: #f5f5f5; border-radius: 8px; padding: 12px 15px; border: 1px solid #ddd; position: relative;">
                    <i class="icon-magnifier" style="color: #777; font-size: 18px; margin-right: 10px;"></i>
                    <input type="search" class="search-input" wire:model.live="query" placeholder="Search products, categories..." style="flex: 1; background: transparent; border: none; outline: none; color: #333; font-size: 16px; padding-right: 30px;">
                    <button class="clear-search" style="position: absolute; right: 15px; background: none; border: none; color: #999; cursor: pointer; display: none;">&times;</button>
                </div>

                @if (!empty($results))
                    <div class="search-results" style="position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #eee; border-radius: 0 0 8px 8px; margin-top: 5px; max-height: 400px; overflow-y: auto; z-index: 1000; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                        @foreach ($results as $category => $items)
                            @if ($items->count())
                                <h6 class="mt-2" style="background: #f8f9fa; color: #495057; padding: 8px 15px; margin: 0; font-size: 14px; font-weight: bold; border-bottom: 1px solid #eee;">{{ $category }}</h6>
                                <ul class="list-group" style="list-style: none; padding: 0; margin: 0;">
                                    @foreach ($items as $item)
                                        <li class="list-group-item" style="padding: 10px 15px; border-bottom: 1px solid #eee; cursor: pointer; transition: background 0.2s; background: #fff;">
                                            @if ($category == 'Products')
                                                <a href="{{ route('product.show', $item->slug) }}" class="text-decoration-none" style="display: block; color: #333;">{{ $item->name }}</a>
                                            @elseif ($category == 'Categories')
                                                <a href="{{ route('category.products', $item->slug) }}" class="text-decoration-none" style="display: block; color: #333;">{{ $item->name }}</a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Track modal state
            let isModalOpen = false;

            // Get elements
            const searchToggle = document.querySelector('.search-toggle');
            const searchModal = document.querySelector('.search-modal');
            const closeModal = document.querySelector('.close-modal');
            const modalContent = document.querySelector('.modal-content');
            const searchInput = document.querySelector('.search-input');
            const clearSearchBtn = document.querySelector('.clear-search');

            // Open modal when search icon is clicked
            searchToggle.addEventListener('click', function(e) {
                e.preventDefault();
                isModalOpen = true;
                searchModal.style.display = 'flex';
                setTimeout(() => {
                    searchInput.focus();
                }, 100);
            });

            // Close modal only when explicitly requested
            closeModal.addEventListener('click', function() {
                isModalOpen = false;
                searchModal.style.display = 'none';
            });

            // Prevent closing when clicking inside modal content
            modalContent.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // Close only when clicking on the backdrop (outside modal content)
            searchModal.addEventListener('click', function(e) {
                if (e.target === searchModal) {
                    isModalOpen = false;
                    searchModal.style.display = 'none';
                }
            });

            // Handle Livewire updates
            document.addEventListener('livewire:init', function() {
                Livewire.hook('morph.updated', (el, component) => {
                    // Restore modal state after Livewire updates
                    if (isModalOpen) {
                        searchModal.style.display = 'flex';
                    }
                });
            });

            // Persist modal state during navigation
            Livewire.on('preserve-state', () => {
                if (isModalOpen) {
                    searchModal.style.display = 'flex';
                }
            });

            // Clear search input when X is clicked
            clearSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.focus();
                clearSearchBtn.style.display = 'none';
                // Trigger Livewire update if needed
                if (searchInput.hasAttribute('wire:model')) {
                    searchInput.dispatchEvent(new Event('input'));
                }
            });

            // Show/hide clear button based on input
            searchInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    clearSearchBtn.style.display = 'block';
                } else {
                    clearSearchBtn.style.display = 'none';
                }
            });

            // Also check on focus in case value was set programmatically
            searchInput.addEventListener('focus', function() {
                if (this.value.length > 0) {
                    clearSearchBtn.style.display = 'block';
                }
            });
        });
    </script>

    <style>
        /* Animation for modal */
        .search-modal {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .search-modal[style*="display: flex"] {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .search-modal[style*="display: flex"] .modal-content {
            transform: translateY(0);
        }

        /* Header search icon styles */
        .header-search a {
            display: inline-block;
            padding: 10px;
            color: #333;
        }

        .header-search i {
            font-size: 18px;
        }

        /* Scrollbar styling */
        .search-results::-webkit-scrollbar {
            width: 6px;
        }

        .search-results::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .search-results::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }

        /* Hover effects */
        .list-group-item:hover {
            background: #f8f9fa !important;
        }

        .list-group-item:hover a {
            color: #007bff !important;
        }

        .search-toggle:hover {
            opacity: 0.8;
        }

        /* Ensure modal stays on top of everything */
        .search-modal {
            z-index: 9999 !important;
        }

        /* Better color contrast for category headers */
        .search-results h6 {
            background: #f8f9fa !important;
            color: #495057 !important;
        }

        /* Improved text color for results */
        .search-results a {
            color: #333 !important;
        }

        /* Better background for results container */
        .search-results {
            background: #fff !important;
            border-color: #eee !important;
        }

        /* Individual item styling */
        .list-group-item {
            background: #fff !important;
            border-color: #eee !important;
        }

        /* Clear search button styling */
        .clear-search {
            font-size: 20px;
            padding: 0 5px;
            transition: color 0.2s;
        }

        .clear-search:hover {
            color: #333 !important;
        }
    </style>
</div>
