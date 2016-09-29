//
//  BookingCreditsViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 17/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
#import "PTKView.h"

@interface BookingCreditsViewController : AppBaseViewController<PTKViewDelegate>
{
    __weak IBOutlet UIButton *btn_cancel;
    __weak IBOutlet UITableView *tbl_PackageList;
    NSMutableArray *array_packageList;
    NSMutableArray *array_addValue;
    __weak IBOutlet UILabel *lbl_buyBookingHeading;
    __weak IBOutlet UILabel *lbl_creditsDetail;
    __weak IBOutlet UIButton *btn_addOrder;
    __weak IBOutlet UILabel *lbl_availableCredits;
    
}
@property(nonatomic,assign)Parent *parentInfo;
@property(nonatomic)int checkValue;
- (IBAction)onClickCancleBookingCredits:(id)sender;
- (IBAction)onClickAddToOrderBookingCredits:(id)sender;

@end
