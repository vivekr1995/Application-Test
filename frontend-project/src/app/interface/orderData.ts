/**
 * Data structure defined here
 */
export interface OrderData {
  isSelected: boolean;
  id: number;
  name: string;
  state: string;
  zip: string;
  amount: string;
  quantity: string;
  item: string;
  isEdit: boolean;
  success: boolean;
}

/**
 * Data structure defined for event
 */
export interface EventData {
  checked: boolean;
}

/**
 * Data structure defined for input handler
 */
export interface HandlerData {
  target: any;
}

/**
 * Defining data validation and type
 */
export const OrderColumns = [
  {
    key: 'isSelected',
    type: 'isSelected',
    label: '',
  },
  {
    key: 'name',
    type: 'text',
    label: 'Name',
    required: true,
    pattern: '^[A-Za-z]+$',
  },
  {
    key: 'state',
    type: 'text',
    label: 'State',
    required: true,
    pattern: '^[A-Za-z]+$',
  },
  {
    key: 'zip',
    type: 'text',
    label: 'Zip',
    required: true,
    maxlength: 6,
    pattern: '^[0-9]*$',
  },
  {
    key: 'amount',
    type: 'number',
    label: 'Amount',
    required: true,
    step: 0.01,
  },
  {
    key: 'quantity',
    type: 'number',
    label: 'Quantity',
    required: true,
  },
  {
    key: 'item',
    type: 'text',
    label: 'Item',
    required: true,
    pattern: '^[a-zA-Z0-9]+$',
  },
  {
    key: 'isEdit',
    type: 'isEdit',
    label: '',
  },
];
