//
//  LocalAddressViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 03/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "AppBaseViewController.h"
@interface LocalAddressViewController : AppBaseViewController
{

    __weak IBOutlet UILabel *lbl_zip;
    __weak IBOutlet UILabel *lbl_state;
    __weak IBOutlet UILabel *lbl_city;
    __weak IBOutlet UILabel *lbl_streetAddress;
    __weak IBOutlet UILabel *lbl_crossStreet;
    __weak IBOutlet UILabel *lbl_hotelName;
    __weak IBOutlet UIView *view_localAddress;
    __weak IBOutlet UITableView *tbl_stateList;
    __weak IBOutlet UITextField *txt_hotel_name;
    __weak IBOutlet UITextField *txt_crossStreet;
    __weak IBOutlet UITextField *txt_street_address;
    __weak IBOutlet UITextField *txt_city;
    __weak IBOutlet UITextField *txt_state;
    __weak IBOutlet UITextField *txt_zip;
    __weak IBOutlet UIButton *btn_selectState;
    int checkValue;
    int checkDropState;
    float contentHight;
    NSMutableArray *array_statelist;
    NSMutableArray *array_states;
    NSString *str_stateId;
    NSString *str_stateName;
    NSString *str_addressId;

    
}
@property(nonatomic,assign)Parent *parentInfo;
@property(nonatomic,strong)NSMutableDictionary *dict_profileData;
@property(nonatomic,strong)NSMutableDictionary *dict_loginData;
@property(nonatomic,strong)NSMutableDictionary *dict_savedLocalData;
- (IBAction)onClickStateList:(id)sender;

@end
