import { Component, DefaultIterableDiffer, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { MatTableDataSource } from '@angular/material/table';
import { ConfirmDialogComponent } from './confirm-dialog/confirm-dialog.component';
import { OrderData } from './interface/orderData';
import { OrderService } from './services/order.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
})
export class AppComponent {
  /**
   * Defining table data structure
   */
  dataSource = new MatTableDataSource<OrderData>();
  title: string = 'GreenIT Application Challenge';

  /**
   * Creates an instance of app component.
   * @param dialog
   * @param orderService
   */
  constructor(public dialog: MatDialog, private orderService: OrderService) {}

  /**
   * Sets table data on load
   * @param {void}
   * @return {void} returns nothing
   */
  ngOnInit() {
    this.resetData();
  }

  /**
   * Resets table data
   * @param {void}
   * @return {void} returns nothing
   */
  resetData() {
    this.orderService.getOrders().subscribe((res : OrderData[]) => {
      this.dataSource.data = res;
    })
  }

  /**
   * Creates field for new entry
   * @param {void}
   * @return {void} returns nothing
   */
  addRow() {
    const newRow: OrderData = {
      id: 0,
      name: '',
      state: '',
      zip: '',
      amount: '',
      quantity: '',
      item: '',
      isEdit: true,
      isSelected: false,
      success: true
    }
    this.dataSource.data = [newRow, ...this.dataSource.data]
  }

  /**
   * Removing multiple selected data from table
   * @param {void} 
   * @return {void} returns nothing
   */
  removeSelectedRows() {
    const orders = this.dataSource.data.filter((u: OrderData) => u.isSelected)
    
    if(orders.length != 0) {
      // Shows confirmation dialog for selection
      this.dialog
      .open(ConfirmDialogComponent)
      .afterClosed()
      .subscribe((confirm) => {
        // If delete confirmed and shows messages
        if (confirm) {
          this.orderService.deleteOrders(orders).subscribe(() => {
            this.dataSource.data = this.dataSource.data.filter(
              (u: OrderData) => !u.isSelected,
            )
          })

          this.orderService.showSuccessMessage('Data removed successfully', 'Deleted');
          
        }
      })
    } else {
      // Shows error messages
      this.orderService.showErrorMessage('Select any data', 'No data selected!');
      
    }
    
  }

}
