import { Component, Input, Output, EventEmitter, ViewChild } from '@angular/core';
import { MatTableDataSource } from '@angular/material/table';
import { OrderData, OrderColumns, EventData, HandlerData } from '../interface/orderData';
import { OrderService } from '../services/order.service';
import { Sort } from '@angular/material/sort';
import { MatDialog } from '@angular/material/dialog';
import { ConfirmDialogComponent } from '../confirm-dialog/confirm-dialog.component';
import { MatPaginator} from '@angular/material/paginator';

import { forkJoin, Observable, throwError, map, retry, catchError, Observer } from 'rxjs';

@Component({
  selector: 'table-data',
  templateUrl: './table-data.component.html',
  styleUrls: ['./table-data.component.scss'],
})
export class TableDataComponent {
  displayedColumns: string[] = OrderColumns.map((col) => col.key);
  columnsSchema: any = OrderColumns;

  /**
   * get dataSource data from parent component
   */
  @Input() dataSource = new MatTableDataSource<OrderData>();
  @Output() reset = new EventEmitter<boolean>();

  valid: Array<any> = [];
  is_disabled: boolean = false;
  sortedData: OrderData[];

  // Get paginator from view
  @ViewChild('paginator') paginator!: MatPaginator;

  /**
   * Creates an instance of table component.
   * @param orderService
   * @param dialog
   */
  constructor(private orderService: OrderService, public dialog: MatDialog) {
    this.sortedData = this.dataSource.data.slice();
  }

  /**
   * To declare paginator values
   * @param {void}
   * @return {void} returns nothing
   */
  ngAfterViewInit(): void {
      this.dataSource.paginator = this.paginator;
  }

  /**
   * Datatable content reset, child-parent interaction through emit
   * @param {void} nothing
   * @return {void} returns nothing
   */
  resetItem() {
    this.reset.emit(true);
  }

  /**
   * Add and edit table data by passing row data and shows messages
   * @param row
   * @return {void} returns nothing
   */
  editRow(row: OrderData) {
    //New entry
    if (row.id === 0) {
        //Validating required data
        if(row.name == '' || row.zip == '' || row.amount == '' || row.quantity == '' || row.item == '') {
            //Button disabled and showing alert message
            this.is_disabled = true;

            // Showing error message
            this.orderService.showErrorMessage('The required fields could not Blank', 'Required!');
            
        } else {
            if(this.dataSource.data) {
                var is_unique = true;
                //Checking item already exists
                this.dataSource.data.forEach(element => {
                    if(element.id != 0 && element.item == row.item) {
                        is_unique = false;
                    }
                });

                if(is_unique) {
                    //Adding new entry
                    this.orderService.addOrder(row).subscribe((response: OrderData) => {
                      if(response.success) {

                        // Showing success message
                        this.orderService.showSuccessMessage('Data added successfully', 'Added');

                        // Table Data reseting after success
                        this.resetItem();
                      } else {
                        // Data add faled message showing
                        this.orderService.showErrorMessage('Data add failed', 'Failed!');
                        
                      }
                        
                    })
                    
                } else {
                    //Showing error message
                    this.is_disabled = true;
                    this.orderService.showErrorMessage('Item should be unique', 'Not Unique!');
                    
                }
            }
        }
    } else {
        //Updating existing data
        this.orderService.updateOrder(row).subscribe((response: OrderData) => {
          if(response.success) {
            // Showing update success message and data resetting
            this.orderService.showSuccessMessage('Data updated successfully', 'Updated');
            
            this.resetItem();
          } else {
            // Showing update failed message
            this.orderService.showErrorMessage('Data update failed', 'Failed!');
            
          }
        })
    }
  }

  /**
   * Single data delete and shows messages
   * @param id
   * @return {void} returns nothing
   */
  removeRow(id: number) {
    /**
     * Shows confirmation dialog selection
     */
    this.dialog
      .open(ConfirmDialogComponent)
      .afterClosed()
      .subscribe((confirm) => {
        // If selected confirm
        if (confirm) {
          // Calls delete api
          this.orderService.deleteOrder(id).subscribe((response: OrderData) => {
            // Showing success and failed message
            if(response.success) {
              this.dataSource.data = this.dataSource.data.filter(
                (u: OrderData) => u.id !== id,
              )
              this.orderService.showSuccessMessage('Data removed successfully', 'Deleted');
              
            } else {
              this.orderService.showErrorMessage('Data remove failed', 'Failed!');
              
            }
              
          })
        }
    })
  }

  /**
   * Cancel add or edit actions
   * @param row
   * @return {void} returns nothing
   */
  cancelAddEditRow(row: OrderData) {
    this.resetItem();
  }

  /**
   * Data validity check
   * @param event
   * @param id
   * @param key
   * @return {void} returns nothing
   */
  inputHandler(e: HandlerData, id: number, key: string) {
    
    this.is_disabled = false;
    if (!this.valid[id]) {
      this.valid[id] = {};
    }
    this.valid[id][key] = e.target.validity.valid;
  }

  /**
   * Disabling submit button
   * @param id
   * @returns boolean
   */
  disableSubmit(id: number) {
    if (this.valid[id]) {
      return Object.values(this.valid[id]).some((item) => item === false)
    }
    return false
  }

  /**
   * Checks all rows are selected
   * @return boolean
   */
  isAllSelected() {
    return this.dataSource.data.every((item) => item.isSelected)
  }

  /**
   * Checks if any rows are selected
   * @param {void} nothing
   * @return boolean
   */
  isAnySelected() {
    return this.dataSource.data.some((item) => item.isSelected)
  }

  /**
   * Selects all rows 
   * @param event
   * @return {void} returns nothing
   */
  selectAll(event: EventData) {
    this.dataSource.data = this.dataSource.data.map((item) => ({
      ...item,
      isSelected: event.checked,
    }))
  }

  /**
   * Data gets sorted here
   * @param sort event
   */
  sortData(sort: Sort) {
    const data = this.dataSource.data.slice();
    if (!sort.active || sort.direction === '') {
      this.sortedData = data;
      return;
    }

    //Comparing each element
    this.sortedData = data.sort((a, b) => {
      const isAsc = sort.direction === 'asc';
      switch (sort.active) {
        case 'name':
          return compare(a.name, b.name, isAsc);
        case 'state':
          return compare(a.state, b.state, isAsc);
        case 'zip':
          return compare(a.zip, b.zip, isAsc);
        case 'amount':
          return compare(a.amount, b.amount, isAsc);
        case 'quantity':
          return compare(a.quantity, b.quantity, isAsc);
        case 'item':
          return compare(a.item, b.item, isAsc);
        default:
          return 0;
      }
    });

    this.dataSource.data = this.sortedData;
  }

  /**
   * Global Search for data given
   * @param event
   * @return {void} returns nothing
   */
  applyFilter(event: Event) {
    
    const filterValue = (event.target as HTMLInputElement).value;
    this.dataSource.filter = filterValue.trim().toLowerCase();
  }

}

/**
 * Comparing data for search
 * @param a
 * @param b
 * @param isAsc
 * @return boolean
 */
function compare(a: number | string, b: number | string, isAsc: boolean) {
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
