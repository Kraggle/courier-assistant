<x-layout.error title="Forbidden"
  :code="403"
  :message="$exception->getMessage() ?: 'Forbidden'" />
