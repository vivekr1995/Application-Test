//Data structure
export interface User {
  isSelected: boolean;
  id: number;
  name: string;
  state: string;
  zip: string;
  amount: string;
  qty: string;
  item: string;
  isEdit: boolean;
}

//Defining data validation and type
export const UserColumns = [
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
  },
  {
    key: 'zip',
    type: 'text',
    label: 'Zip',
    required: true,
    maxlength: 5,
  },
  {
    key: 'amount',
    type: 'number',
    label: 'Amount',
    required: true,
    step: 0.01,
  },
  {
    key: 'qty',
    type: 'number',
    label: 'Qty',
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
